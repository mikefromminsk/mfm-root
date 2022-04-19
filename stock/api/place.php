<?php

include_once "auth.php";

$ticker = get_required_uppercase("ticker");
$is_sell = get_int_required("is_sell");
$price = get_int_required("price");
$amount = get_int_required("amount");

$response[order_id] = place($user_id, $ticker, $is_sell, $price, $amount);

$response[result] = $response[order_id] != null;

echo json_encode($response);