<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/auth.php";

$limit = get_int("limit", 1);
$ticker = get_required_uppercase("ticker");
$is_sell = get_int_required("is_sell");
$price = get_int_required("price");
$amount = get_int_required("amount");

$currency = selectRow("currencies", ["ticker" => $ticker]);
if ($currency == null) error("ticker is not found");

$coin_balance = selectRowWhere("balances", ["user_id" => $user_id, "ticker" => $ticker]);
$usdt_balance = selectRowWhere("balances", ["user_id" => $user_id, "ticker" => "USDT"]);

if ($is_sell == 1) {
    $filled = 0;
    if ($coin_balance["spot"] < $amount) error("balance is not enough");
    updateWhere("balances", ["spot" => $coin_balance["spot"] - $amount], ["user_id" => $user_id, "ticker" => $ticker]);
    foreach (select("select * from orders where ticker == '$ticker' and price >= $price and status >= 0 order by price,timestamp") as $order) {
        $not_filled = $amount - $filled;
        $order_not_filled = $order["amount"] - $order["filled"];
        $coin_to_fill = min($not_filled, $order_not_filled);
        $usdt_to_fill = $coin_to_fill * $order["price"];
        updateWhere("orders", ["filled" => $order["filled"] + $coin_to_fill, "status" => $order_not_filled == $coin_to_fill ? 1 : 0], ["order_id" => $order["order_id"]]);
        $order_usdt_balance = selectRowWhere("balances", ["user_id" => $order["user_id"], "ticker" => "USDT"]);
        $order_coin_balance = selectRowWhere("balances", ["user_id" => $order["user_id"], "ticker" => $ticker]);
        updateWhere("balances", ["blocked" => $order_usdt_balance["blocked"] - $usdt_to_fill], ["user_id" => $order["user_id"], "ticker" => "USDT"]);
        updateWhere("balances", ["spot" => $order_coin_balance["spot"] + $coin_to_fill], ["user_id" => $order["user_id"], "ticker" => $ticker]);

        $usdt_balance["spot"] += $usdt_to_fill;
        updateWhere("balances", ["spot" => $usdt_balance["spot"]], ["user_id" => $order["user_id"], "ticker" => "USDT"]);
        $filled += $coin_to_fill;
        if ($filled == $amount)
            break;
    }
    $response["order_id"] = insertRowAndGetId("orders", ["ticker" => $ticker, "user_id" => $user_id, "is_sell" => $is_sell, "price" => $price, "amount" => $amount, "timestamp" => time(), "filled" => $filled, "status" => $filled == $amount ? 1 : 0]);
} else {
}

echo json_encode($response);