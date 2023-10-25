<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$wallet_path = get_required(wallet_path, "data/wallet");
$address = get_required(address);

$wallet_path = explode("/", $wallet_path);

if (!dataExist($wallet_path, $address)) error("wallet not exist");

$response[next_hash] = dataGet($wallet_path, [$address, next_hash]);
$response[amount] = dataGet($wallet_path, [$address, amount]);
$response[prev_key] = dataGet($wallet_path, [$address, prev_key]);

commit($response);
