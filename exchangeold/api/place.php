<?php

include_once "auth.php";

$ticker = get_required_uppercase("ticker");
$is_sell = get_int_required("is_sell");
$price = get_int_required("price");
$amount = get_int_required("amount");

$order_id = place($user_id, $ticker, $is_sell, $price, $amount);

if ($order_id == null) error("place error");

$response["result"] = true;

include_once "orders.php";