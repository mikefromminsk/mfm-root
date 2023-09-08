<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$from_address = get_required(from_address);
$to_address = get_required(to_address);
$password = get_required(password);
$next_hash = get_required(next_hash);
$amount = get_int_required(amount);

dataWalletSend([usdt, wallet], $from_address, $to_address, $amount, $password, $next_hash);

$response[sended] = $amount;
$response[reuslt] = true;

echo json_encode($response);
