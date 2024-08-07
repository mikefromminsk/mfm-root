<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/exchange/api/exchange/utils.php";

$is_sell = get_int_required(is_sell);
$address = get_required(address);
$price = get_int_required(price);
$amount = get_int_required(amount);
$key = get_required(key);
$next_hash = get_required(next_hash);

$order_id = place($address, $is_sell, $price, $amount, $key, $next_hash);

if ($order_id == null) error("place error");

$response[result] = true;

commit($response, exchange_ . getDomain() . _gas);