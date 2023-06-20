<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$address = get_required(address);
$next_hash = get_required(next_hash);

dataWalletReg([usdt, wallet], $address, $next_hash);

$response[result] = true;

echo json_encode($response);
