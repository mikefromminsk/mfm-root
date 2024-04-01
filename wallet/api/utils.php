<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/analytics.php";

$gas_domain = "data";

function dataWalletSettingsSave($user, $key, $value)
{
    $values = dataHistory([wallet, settings, $user, $key]) ?: [];
    if (array_search($value, $values) === false)
        dataSet([wallet, settings, $user, $key], $value);
    return dataGet([wallet, settings, $user, $key]) == $value;
}

function dataWalletSettingsRead($user, $key)
{
    return dataHistory([wallet, settings, $user, $key]) ?: [];
}

function dataWalletReg($address, $next_hash, $domain = null)
{
    if ($domain == null)
        $domain = getDomain();
    $path = $domain . "/wallet";
    if (dataExist([$path, $address])) error("$path/$address exist");
    dataSet([$path, $address, next_hash], $next_hash);
    dataWalletSettingsSave($address, domains, $domain);
    trackSum($domain, wallets, 1);
    return dataExist([$path, $address, next_hash]);
}

function dataWalletDelegate($domain, $address, $key, $script)
{
    if (!dataExist([$domain, wallet, $address])) error("$address not exist");
    if (dataGet([$domain, wallet, $address, next_hash]) != md5($key)) error("key is not right");
    dataSet([$domain, wallet, $address, script], $script);
    return dataExist([$domain, wallet, $address, script]);
}

function dataWalletBalance($domain, $address)
{
    return dataGet([$domain, wallet, $address, amount]) ?: 0.0;
}

function dataWalletBlocks()
{
    return ceil(time() / 60);
}

function dataWalletSend(
    $domain,
    $from_address,
    $to_address,
    $amount,
    $key = null,
    $next_hash = null
)
{

    //die(json_encode(md5($key)));
    //die(json_encode(dataGet([$path, $from_address, next_hash])));
    if ($amount == 0)
        return true;
    if (dataWalletBalance($domain, $from_address) < $amount)
        error(strtoupper($domain) . " balance is not enough in $from_address wallet");
    if (!dataExist([$domain, wallet, $to_address])) error("$to_address receiver doesn't exist");
    if ($key == null || $next_hash == null) {
        if (dataGet([$domain, wallet, $from_address, script]) != scriptPath())
            error("script " . scriptPath() . " cannot use $from_address address");
    } else {
        if (dataGet([$domain, wallet, $from_address, next_hash]) != md5($key))
            error("key is not right");
    }

    dataSet([$domain, wallet, $from_address, prev_key], $key);
    dataSet([$domain, wallet, $from_address, next_hash], $next_hash);

    dataDec([$domain, wallet, $from_address, amount], $amount);
    dataInc([$domain, wallet, $to_address, amount], $amount);

    $last_trans = dataGet([$domain, last_trans]);
    $next_trans = md5($last_trans . $from_address . $to_address . $amount);
    $GLOBALS[trans][$domain][] = $next_trans;
    dataSet([$domain, trans, $next_trans], [
        from => $from_address,
        to => $to_address,
        amount => $amount,
    ]);
    dataSet([$domain, last_trans], $next_trans);
    dataSet([$domain, wallet, $from_address, last_trans], $next_trans);
    dataSet([$domain, wallet, $to_address, last_trans], $next_trans);
    trackSum($domain, transfer, $amount);
    return $next_trans;
}

function getTran($domain, $txid)
{
    $gas = 0;
    if ($domain != $GLOBALS[gas_domain]) {
        $gasTxid = dataGet([$domain, trans, $txid, gas]);
        $gas = dataGet([$GLOBALS[gas_domain], trans, $gasTxid, amount]);
    }
    return [
        domain => $domain,
        txid => $txid,
        from => dataGet([$domain, trans, $txid, from]),
        to => dataGet([$domain, trans, $txid, to]),
        amount => dataGet([$domain, trans, $txid, amount]),
        time => dataInfo([$domain, trans, $txid])[data_time],
        gas => $gas,
    ];
}

function commit($response, $gas_address = null)
{
    if ($GLOBALS[gas_bytes] != 0) {
        $trans = $GLOBALS[trans];
        foreach ($trans as $domain => $txids)
            $GLOBALS[gas_bytes] += count($txids);
        if ($gas_address != null) {
            $dataTxid = dataWalletSend(
                $GLOBALS[gas_domain],
                $gas_address,
                admin,
                DEBUG ? 1 : $GLOBALS[gas_bytes]);
        } else {
            $dataTxid = dataWalletSend(
                $GLOBALS[gas_domain],
                get_required(gas_address),
                admin,
                DEBUG ? 1 : $GLOBALS[gas_bytes],
                get_required(gas_key),
                get_required(gas_next_hash)
            );
        }
        foreach ($trans as $domain => $txids) {
            foreach ($txids as $txid) {
                dataSet([$domain, trans, $txid, gas], $dataTxid);
            }
        }
        dataCommit();
        $response[gas_spend] = $GLOBALS[gas_bytes];
        $response[gas_txid] = $dataTxid;
    }
    echo json_encode($response);
}

function installApp($domain, $app_domain, $filepath = null)
{
    if ($app_domain != null && $filepath == null) {
        $filepath = $_SERVER[DOCUMENT_ROOT] . "/wallet/apps/$app_domain.zip";
    }
    $file_hash = hash_file(md5, $filepath);
    if (!$file_hash) error("file hash is false in $filepath");
    if (dataGet([$domain, packages, $app_domain, hash]) == $file_hash) error("archive was uploaded before");

    $zip = new ZipArchive;
    if ($zip->open($filepath) !== true) error("zip->open is false");

    $zip->extractTo($_SERVER[DOCUMENT_ROOT] . DIRECTORY_SEPARATOR . $domain);

    $files = [];
    $hasRootIndex = false;
    $hasConsole = false;
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $filename = $zip->getNameIndex($i);
        if ($filename == "index.html") $hasRootIndex = true;
        if ($filename == "console/index.html") $hasConsole = true;
        $filepath = $domain . "/" . $filename;
        $filepath = implode("/", explode("\\", $filepath));
        $file_hash = hash_file(md5, $_SERVER[DOCUMENT_ROOT] . DIRECTORY_SEPARATOR . $filepath);
        $files[$file_hash] = $filepath;
        $GLOBALS[gas_bytes] += 1;
    }
    dataSet([$domain, packages, $app_domain, hash], $file_hash);
    dataSet([wallet, info, $domain, ui], $hasRootIndex ? 1 : 0);
    dataSet([wallet, info, $domain, console], $hasConsole ? 1 : 0);
    dataSet([wallet, info, $domain, contracts], $files);
    $zip->close();

    return $files;
}

function uploadContent($domain, $filepath, $local_path)
{
    $zip = new ZipArchive;
    if ($zip->open($filepath) !== true) error("zip->open is false");

    for ($i = 0; $i < $zip->numFiles; $i++) {
        if (!in_array(pathinfo($zip->getNameIndex($i))[extension], [jpg, svg])) {
            error("file extension is not correct");
        }
    }
    $domain_folder = $_SERVER[DOCUMENT_ROOT] . DIRECTORY_SEPARATOR . $domain . DIRECTORY_SEPARATOR;
    $local_folder = $domain_folder . $local_path . DIRECTORY_SEPARATOR;
    $temp_folder = $local_folder . temp . DIRECTORY_SEPARATOR;
    $zip->extractTo($temp_folder);

    $files = [];
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $extension = strtolower(pathinfo($zip->getNameIndex($i))[extension]);
        $hash = hash_file(md5, $temp_folder . $zip->getNameIndex($i));
        $target_filename = "$local_folder$hash.$extension";
        mkdir($local_folder, 0777, true);
        unlink($target_filename);
        rename($temp_folder . $zip->getNameIndex($i), $target_filename);
        $files[$hash] = $hash . "." . $extension;
    }

    $zip->close();
    return $files;
}

function hasNft($domain, $address, $item_hash)
{
    return dataGet([$domain, nft, wallet, $address, $item_hash, count]) > 0;
}

function dataIcoSell($key, $next_hash, $amount, $price)
{
    $domain = getDomain();
    $owner_address = dataGet([wallet, info, $domain, owner]);
    if (!dataExist(["usdt/wallet", $owner_address])) error("usdt address is not init");
    if (!dataExist([$domain, wallet, ico])) {
        dataWalletReg(ico, md5(pass), $domain);
        $contract_path = dataGet([wallet, info, $domain, contracts, "d670072f06bf06183fb422b9c28f1d8b"]);
        dataWalletDelegate($domain, ico, pass, $contract_path);
    }
    dataWalletSend($domain, $owner_address, ico, $amount, $key, $next_hash);
    dataSet([$domain, price], $price);
    trackValue($domain, price, $price);
}

function dataIcoBuy($to_address, $key, $next_hash, $amount)
{
    $domain = getDomain();
    $owner_address = dataGet([wallet, info, $domain, owner]);
    $token_price = dataGet([$domain, price]);
    $total_usdt = $amount * $token_price;

    dataWalletSend("usdt", $to_address, $owner_address, $total_usdt, $key, $next_hash);
    dataWalletSend($domain, ico, $to_address, $amount);
    trackSum($domain, volume, $amount);
}

function dataWalletBonusCreate($domain,
                               $address,
                               $key,
                               $next_hash,
                               $amount,
                               $invite_next_hash)
{
    if (dataExist([$domain, invite, $invite_next_hash])) error("drop exists");
    if (!dataExist([$domain, wallet, bonus])) {
        dataWalletReg(bonus, md5(pass), $domain);
        $bonus_receive_contract_hash = dataGet([wallet, info, $domain, contracts, "96eb30f335960041368dc63ee5e6ebec"]);
        dataWalletDelegate($domain, bonus, pass, $bonus_receive_contract_hash);
    }
    dataWalletSend($domain, $address, bonus, $amount, $key, $next_hash);
    dataSet([$domain, bonus, $invite_next_hash, amount], $amount);
    return true;
}

function dataWalletBonusReceive($domain,
                                $invite_key,
                                $to_address)
{
    $invite_hash = md5($invite_key);
    $amount = dataGet([$domain, bonus, $invite_hash, amount]);
    if ($amount == null) error("hash is not right");
    dataWalletSend($domain, bonus, $to_address, $amount);
    dataSet([$domain, bonus, $invite_hash, amount], 0);
    return $amount;
}
