<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$address = get_path_required(address);

$wallet_path = getDomain() . "/wallet";

if (!dataExist([$wallet_path, $address])) error("wallet not exist");

$response[next_hash] = dataGet([$wallet_path, $address, next_hash]);
$response[amount] = dataGet([$wallet_path, $address, amount]);
$response[prev_key] = dataGet([$wallet_path, $address, prev_key]);
$response[script] = dataGet([$wallet_path, $address, script]);

commit($response);
