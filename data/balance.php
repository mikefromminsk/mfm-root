<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$wallet_path = get_required(wallet_path, "data/wallet");
$address = get_required(address);

$wallet_path = explode("/", $wallet_path);

$response = dataWalletBalance($wallet_path, $address);

echo json_encode($response);
