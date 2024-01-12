<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/analytics.php";

$gas_domain = "data";

function dataWalletReg($address, $next_hash, $domain = null)
{
    if ($domain == null)
        $domain = getDomain();
    $path = $domain . "/wallet";
    if (dataExist([$path, $address])) error("$path/$address exist");
    dataSet([$path, $address, next_hash], $next_hash);
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

function dataWalletSend($domain, $from_address, $to_address, $amount, $key = null, $next_hash = null)
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
    dataSet([$domain, trans, $next_trans], [
        from => $from_address,
        to => $to_address,
        amount => $amount,
    ]);
    dataSet([$domain, last_trans], $next_trans);
    dataSet([$domain, wallet, $from_address, last_trans], $next_trans);
    dataSet([$domain, wallet, $to_address, last_trans], $next_trans);
    trackSum($domain, transfer, $amount);
    return true;
}

function commit($response, $gas_address = null)
{
    if ($GLOBALS[gas_bytes] != 0) {
        if ($gas_address != null) {
            dataWalletSend(
                $GLOBALS[gas_domain],
                $gas_address,
                admin,
                DEBUG ? 1 : $GLOBALS[gas_bytes]);
        } else {
            dataWalletSend(
                $GLOBALS[gas_domain],
                get_required(gas_address),
                admin,
                DEBUG ? 1 : $GLOBALS[gas_bytes],
                get_required(gas_key),
                get_required(gas_next_hash)
            );
        }
        dataCommit();
    }
    echo json_encode($response);
}

function upload($domain, $filepath)
{
    $file_hash = hash_file(md5, $filepath);

    //if (dataGet([$domain, vote, value]) != $file_hash) error("votes not equal to file");
    if (dataGet([$domain, hash]) == $file_hash) error("archive was uploaded before");

    $zip = new ZipArchive;
    if ($zip->open($filepath) !== TRUE) error("zip->open is false");

    $zip->extractTo($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . $domain);
    dataSet([$domain, vote, last_uploaded], $file_hash);

    $files = [];
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $filepath = $domain . "/" . $zip->getNameIndex($i);
        $filepath = implode("/", explode("\\", $filepath));
        $file_hash = hash_file(md5, $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . $filepath);
        $files[$file_hash] = $filepath;
    }
    dataSet([store, $domain], $files);
    $zip->close();

    return $files;
}

function dataIcoSell($key, $next_hash, $amount, $price)
{
    $domain = getDomain();
    $owner_address = dataGet([wallet, info, $domain, owner]);
    if (!dataExist(["usdt/wallet", $owner_address])) error("usdt address is not init");
    if (!dataExist([$domain, wallet, ico])) {
        dataWalletReg(ico, md5(pass), $domain);
        $contract_path = dataGet([store, $domain, "d670072f06bf06183fb422b9c28f1d8b"]);
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
        $bonus_receive_contract_hash = dataGet([store, $domain, "96eb30f335960041368dc63ee5e6ebec"]);
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