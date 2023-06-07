<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$fromAddress = get_required(fromAddress);
$toAddress = get_required(toAddress);
$password = get_required(password);
$next_hash = get_required(next_hash);
$amount = get_int_required(amount);

dataSend([usdt, wallet], $fromAddress, $toAddress, $password, $next_hash, $amount);

$response[reuslt] = true;

echo json_encode($response);
