<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$address = get_required(address);
$password = get_required(password);
$next_hash = get_required(next_hash);
$path = get_required(path);
$type = get_required(type);
$value = get_required(value);

$amount = 1;
dataWalletSend([data, wallet], $address, admin, $amount, $password, $next_hash);
$response[spended] = $amount;
$response[result] = dataSet(explode("/", $path), $value);

echo json_encode($response);