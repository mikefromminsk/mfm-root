<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$key = get_string(key);
$next_hash = get_required(next_hash);
$amount = get_int_required(amount);
$price = get_int_required(price);

dataIcoSell($key, $next_hash, $amount, $price);

$response[success] = true;

commit($response);