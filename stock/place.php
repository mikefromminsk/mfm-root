<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/auth.php";

$ticker = get_required_uppercase("ticker");
$is_sell = get_int_required("is_sell");
$price = get_int_required("price");
$amount = get_int_required("amount");
$total = $price * $amount;


if ($is_sell == 1) {
    if (!haveBalance($user_id, $ticker, $amount)) error("not enough balance");
    $not_filled = $amount;
    decBalance($user_id, $ticker, $amount);
    foreach (select("select * from orders where ticker = '$ticker' and is_sell = 0 and price >= $price and status = 0 order by price DESC,timestamp") as $order) {
        $order_not_filled = $order["amount"] - $order["filled"];
        $coin_to_fill = min($not_filled, $order_not_filled);
        $usdt_to_fill = $coin_to_fill * $order["price"];
        updateWhere("orders", [filled => $order["filled"] + $coin_to_fill, status => $order_not_filled == $coin_to_fill ? 1 : 0], [order_id => $order["order_id"]]);
        unbBalance($order["user_id"], USDT, $usdt_to_fill);
        incBalance($order["user_id"], $ticker, $coin_to_fill);
        incBalance($user_id, USDT, $usdt_to_fill);
        $not_filled -= $coin_to_fill;
        if ($not_filled == 0)
            break;
    }
    blkBalance($user_id, $ticker, $not_filled);
    $response["order_id"] = insertRowAndGetId("orders", [user_id => $user_id, ticker => $ticker, is_sell => $is_sell, price => $price, amount => $amount, filled => $amount - $not_filled, status => $not_filled == 0 ? 1 : 0, timestamp => time()]);
} else {
    if (!haveBalance($user_id, USDT, $total)) error("not enough balance");
    $not_filled = $amount;
    foreach (select("select * from orders where ticker = '$ticker' and is_sell = 1 and price <= $price and status = 0 order by price,timestamp") as $order) {
        $order_not_filled = $order["amount"] - $order["filled"];
        $coin_to_fill = min($not_filled, $order_not_filled);
        $usdt_to_fill = $coin_to_fill * $order["price"];
        updateWhere("orders", [filled => $order["filled"] + $coin_to_fill, status => $order_not_filled == $coin_to_fill ? 1 : 0], [order_id => $order["order_id"]]);
        unbBalance($order["user_id"], $ticker, $coin_to_fill);
        incBalance($order["user_id"], USDT, $usdt_to_fill);
        incBalance($user_id, $ticker, $coin_to_fill);
        decBalance($user_id, USDT, $usdt_to_fill);
        $not_filled -= $coin_to_fill;
        if ($not_filled == 0)
            break;
    }
    decBalance($user_id, USDT, $not_filled * $price);
    blkBalance($user_id, USDT, $not_filled * $price);
    $response["order_id"] = insertRowAndGetId("orders", [user_id => $user_id, ticker => $ticker, is_sell => $is_sell, price => $price, amount => $amount, filled => $amount - $not_filled, status => $not_filled == 0 ? 1 : 0, timestamp => time()]);
}

$response["result"] = $response["order_id"] != null;
echo json_encode($response);