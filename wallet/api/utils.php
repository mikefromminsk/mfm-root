<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/utils.php";

function dataWalletInit(array $path, $address, $next_hash, $amount)
{
    if (dataExist($path)) error("path " . implode("/", $path) . " exist");
    dataWalletReg($path, $address, $next_hash);
    dataSet(array_merge($path, [$address, amount]), $amount);
    return true;
}

function dataWalletReg(array $path, $address, $next_hash)
{
    if (dataExist(array_merge($path, [$address]))) error(implode("/", $path) . "/$address exist");
    dataSet(array_merge($path, [$address, next_hash]), $next_hash);
    return dataExist($path, [$address, next_hash]);
}

function dataWalletDelegate(array $path, $address, $key, $script)
{
    if (!dataExist(array_merge($path, [$address]))) error(implode("/", $path) . "/$address not exist");
    if (dataGet(array_merge($path, [$address, next_hash])) != md5($key)) error("key is not right");
    dataSet(array_merge($path, [$address, script]), $script);
    return dataExist($path, [$address, script]);
}

function dataWalletBalance(array $path, $address)
{
    return dataGet(array_merge($path, [$address, amount])) ?: 0.0;
}

function dataWalletSend(array $path, $from_address, $to_address, $amount, $key = null, $next_hash = null)
{
    if ($amount == 0)
        return true;

    if (dataWalletBalance($path, $from_address) < $amount)
        error(implode("/", $path) . "/$from_address balance is not enough");
    if (!dataExist($path, $to_address)) error("receiver not exist");
    if ($key == null || $next_hash == null) {
        if (dataGet(array_merge($path, [$from_address, script])) != dataAppName())
            error("script cannot use " . implode("/", $path) . "/$from_address address");
    } else {
        if (dataGet(array_merge($path, [$from_address, next_hash])) != md5($key))
            error("key is not right");
    }

    dataSet(array_merge($path, [$from_address, key]), $key);
    dataSet(array_merge($path, [$from_address, next_hash]), $next_hash);

    dataDec(array_merge($path, [$from_address, amount]), $amount);
    dataInc(array_merge($path, [$to_address, amount]), $amount);

    /*dataAdd[transactions]*/
    return true;
}

function commit($response, $gas_address = null)
{
    if ($GLOBALS["gas_bytes"] != 0)
        if ($gas_address != null) {
            dataWalletSend([data, wallet], $gas_address, admin, $GLOBALS["gas_bytes"]);
        } else {
            dataWalletSend(
                [data, wallet],
                get_required(gas_address),
                admin,
                $GLOBALS["gas_bytes"],
                get_required(gas_key),
                get_required(gas_next_hash)
            );
        }
    echo json_encode($response);
}

function dataWalletHash($path, $username, $password){
    return md5(md5($path . $username . $password));
}