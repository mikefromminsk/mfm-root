<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);
$address = get_required(address);
$password = get_required(password);

if (!DEBUG) error("cannot use not in debug session");

function dataWalletKey($path, $username, $password, $prev_key = "")
{
    return md5($path . $username . $password . $prev_key);
}

function dataWalletHash($path, $username, $password, $prev_key = "")
{
    return md5(dataWalletKey($path, $username, $password, $prev_key));
}

$prev_key = dataGet([$domain, wallet, $address, prev_key]);

dataSet([$domain, wallet, $address, next_hash], dataWalletHash($domain, $address, $password, $prev_key));

$response[success] = true;

commit($response);