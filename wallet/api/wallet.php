<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_string(domain);
$address = get_path_required(address);

description("Get wallet");

$token_path = $domain . "/token";

if (!dataExist([$token_path, $address])) error("wallet not exist");

$response[next_hash] = dataGet([$token_path, $address, next_hash]);
$response[amount] = dataGet([$token_path, $address, amount]);
$response[prev_key] = dataGet([$token_path, $address, prev_key]);
$response[script] = dataGet([$token_path, $address, script]);

commit($response);
