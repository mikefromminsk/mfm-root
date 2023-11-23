<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/utils.php";

function dataWalletReg($path, $address, $next_hash)
{
    if (dataExist([$path, $address])) error(implode("/", $path) . "/$address exist");
    dataSet([$path, $address, next_hash], $next_hash);
    return dataExist([$path, $address, next_hash]);
}

function dataWalletDelegate($path, $address, $key, $script)
{
    if (!dataExist([$path, $address])) error(implode("/", $path) . "/$address not exist");
    if (dataGet([$path, $address, next_hash]) != md5($key)) error("key is not right");
    dataSet([$path, $address, script], $script);
    return dataExist([$path, $address, script]);
}

function dataWalletBalance($path, $address)
{
    return dataGet([$path, $address, amount]) ?: 0.0;
}

function dataWalletSend($path, $from_address, $to_address, $amount, $key = null, $next_hash = null)
{
    if ($amount == 0)
        return true;
    if (dataWalletBalance($path, $from_address) < $amount)
        error("$from_address balance is not enough in wallet $path");
    if (!dataExist([$path, $to_address])) error("$to_address receiver doesn't exist");
    if ($key == null || $next_hash == null) {
        if (dataGet([$path, $from_address, script]) != scriptPath())
            error("script " . scriptPath() . " cannot use $path/$from_address address");
    } else {
        if (dataGet([$path, $from_address, next_hash]) != md5($key))
            error("key is not right");
    }

    dataSet([$path, $from_address, prev_key], $key);
    dataSet([$path, $from_address, next_hash], $next_hash);

    dataDec([$path, $from_address, amount], $amount);
    dataInc([$path, $to_address, amount], $amount);

    $domain = explode("/", $path)[0];
    $last_trans = dataGet([$domain, last_trans]);
    $next_trans = md5($last_trans . $from_address . $to_address . $amount);
    dataSet([$domain, trans, $next_trans], [
        from => $from_address,
        to => $to_address,
        amount => $amount,
    ]);
    dataSet([$domain, last_trans], $next_trans);
    dataSet([$path, $from_address, last_trans], $next_trans);
    dataSet([$path, $to_address, last_trans], $next_trans);
    return true;
}

function commit($response, $gas_address = null)
{
    if ($GLOBALS[gas_bytes] != 0)
        if ($gas_address != null) {
            dataWalletSend("data/wallet",
                $gas_address,
                admin,
                $GLOBALS[gas_bytes]);
        } else {
            dataWalletSend(
                "data/wallet",
                get_required(gas_address),
                admin,
                $GLOBALS[gas_bytes],
                get_required(gas_key),
                get_required(gas_next_hash)
            );
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
    $wallet_path = "$domain/wallet";
    $owner_address = dataGet([wallet, info, $domain, owner]);
    if (!dataExist(["usdt/wallet", $owner_address])) error("usdt address is not init");
    if (!dataExist([$wallet_path, ico])) {
        dataWalletReg($wallet_path, ico, md5(pass));
        $contract_path = dataGet([store, $domain, "d670072f06bf06183fb422b9c28f1d8b"]);
        dataWalletDelegate($wallet_path, ico, pass, $contract_path);
    }
    dataWalletSend($wallet_path, $owner_address, ico, $amount, $key, $next_hash);
    dataSet([$domain, price], $price);
}

function dataIcoBuy($to_address, $key, $next_hash, $amount)
{
    $domain = getDomain();
    $owner_address = dataGet([wallet, info, $domain, owner]);
    $token_price = dataGet([$domain, price]);
    $total_usdt = $amount * $token_price;

    dataWalletSend("usdt/wallet", $to_address, $owner_address, $total_usdt, $key, $next_hash);
    dataWalletSend("$domain/wallet", ico, $to_address, $amount);
}

function dataWalletBonusCreate($domain,
                               $from_address,
                               $from_key,
                               $from_next_hash,
                               $amount,
                               $invite_hash)
{
    $wallet_path = $domain . "/wallet";
    $invite_address = $domain . "_bonus";
    if (dataExist([$domain, invite, $invite_hash])) error("drop exists");
    if (!dataExist([$wallet_path, $invite_address])) {
        dataWalletReg($wallet_path, $invite_address, md5(pass));
        $bonus_receive_contract_hash = dataGet([store, $domain, "2e0f34870639c61f4e42053cb34cec9f"]);
        dataWalletDelegate($wallet_path, $invite_address, pass, $bonus_receive_contract_hash);
    }
    dataWalletSend($wallet_path, $from_address, $invite_address, $amount, $from_key, $from_next_hash);
    dataSet([$domain, bonus, $invite_hash, amount], $amount);
    return true;
}

function dataWalletBonusRecieve($domain,
                                $invite_key,
                                $to_address)
{
    $invite_hash = md5($invite_key);
    $amount = dataGet([$domain, bonus, $invite_hash, amount]);
    if ($amount == null) error("hash is not right");
    dataWalletSend($domain . "/wallet", $domain . "_bonus", $to_address, $amount);
    dataSet([$domain, bonus, $invite_hash, amount], 0);
    return $amount;
}