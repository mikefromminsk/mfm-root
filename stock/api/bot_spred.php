<?php
include_once "utils.php";

$time = time();
foreach (selectWhere("coins", [ticker => SOL]) as $coin) {
    $ticker = $coin[ticker];
    $user_id = $coin[user_id];
    $price = $coin[price] ?: 1;
    $spred = 1;
    $support_percent = 10;
    $support_usdt = 10;
    $support_orders = 3;
    $coin_balance = getSpot($user_id, $ticker);
    $usdt_balance = getSpot($user_id, "USDT");
    $sell_orders_count = scalar("select count(*) from orders where user_id = $user_id and ticker = '$ticker' and is_sell = 1 and status = 0");
    $buy_orders_count = scalar("select count(*) from orders where user_id = $user_id and ticker = '$ticker' and is_sell = 0 and status = 0");
    if ($sell_orders_count < $support_orders || $buy_orders_count < $support_orders) {
        cancelAll($user_id, $ticker);
        $best_sell_price = scalar("select price from orders where ticker = '$ticker' and is_sell = 1 and status = 0 order by price limit 1");
        $best_buy_price = scalar("select price from orders where ticker = '$ticker' and is_sell = 0 and status = 0 order by price DESC limit 1");
        if ($best_sell_price != null && $best_buy_price != null)
            $avg_price = ($best_sell_price + $best_buy_price) / 2;
        else if ($best_sell_price == null && $best_buy_price != null)
            $avg_price = $best_buy_price;
        else if ($best_sell_price != null && $best_buy_price == null)
            $avg_price = $best_sell_price;
        else
            $avg_price = $price;
        for ($i = 1; $i <= $support_orders; $i++) {
            $order_price = round($avg_price * (100 - $i * ($support_percent / $support_orders)) / 100, 2);
            $order_amount = round($support_usdt / $support_orders / $order_price, 2);
            if (haveBalance($user_id, $ticker, $order_amount))
                place($user_id, $ticker, 0, $order_price, $order_amount);
        }
        for ($i = 1; $i <= $support_orders; $i++) {
            $order_price = round($avg_price * (100 + $i * ($support_percent / $support_orders)) / 100, 2);
            $order_amount = round($support_usdt / $support_orders / $order_price, 2);
            $order_total = round($order_price * $order_amount, 4);
            if (haveBalance($user_id, USDT, $order_total))
                place($user_id, $ticker, 1, $order_price, $order_amount);
        }
    }
}
