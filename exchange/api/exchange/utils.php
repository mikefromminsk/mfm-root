<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

function place($address, $is_sell, $price, $amount, $key, $next_hash)
{
    $domain = getDomain();
    if ($price != round($price, 2)) error("price tick is 0.01");
    if ($amount != round($amount, 2)) error("amount tick is 0.01");
    $total = round($price * $amount, 4);
    $timestamp = time();

    if ($is_sell == 1) {
        $not_filled = $amount;
        dataWalletSend($domain, $address, exchange, $amount, $key, $next_hash);
        foreach (select("select * from orders where `domain` = '$domain' and is_sell = 0 and price >= $price and status = 0 order by price DESC,timestamp") as $order) {
            $order_not_filled = round($order[amount] - $order[filled], 2);
            $coin_to_fill = min($not_filled, $order_not_filled);
            $order_filled = $order_not_filled == $coin_to_fill ? 1 : 0;
            updateWhere(orders, [filled => $order[filled] + $coin_to_fill, status => $order_filled], [order_id => $order[order_id]]);
            if ($order_filled == 1) {
                dataWalletSend($domain, exchange, $order[username], $order[amount]);
            }
            $not_filled = round($not_filled - $coin_to_fill, 2);
            if ($not_filled == 0)
                break;
        }
        if ($not_filled == 0) {
            dataWalletSend(usdt, exchange_ . $domain, $address, $total);
        }
        $order_id = insertRowAndGetId(orders, [address => $address, domain => $domain, is_sell => $is_sell, price => $price, amount => $amount, filled => $amount - $not_filled, status => $not_filled == 0 ? 1 : 0, timestamp => $timestamp]);
        // analytics
    } else {
        $not_filled = $amount;
        dataWalletSend(usdt, $address, exchange_ . $domain, $total, $key, $next_hash);
        foreach (select("select * from orders where `domain` = '$domain' and is_sell = 1 and price <= $price and status = 0 order by price,timestamp") as $order) {
            $order_not_filled = round($order[amount] - $order[filled], 2);
            $coin_to_fill = min($not_filled, $order_not_filled);
            $order_filled = $order_not_filled == $coin_to_fill ? 1 : 0;
            updateWhere(orders, [filled => $order[filled] + $coin_to_fill, status => $order_filled], [order_id => $order[order_id]]);
            if ($order_filled == 1) {
                dataWalletSend(usdt, exchange_ . $domain, $order[address], round($order[amount] * $order[price], 2));
            }
            $not_filled = round($not_filled - $coin_to_fill, 2);
            if ($not_filled == 0)
                break;
        }
        if ($not_filled == 0) {
            dataWalletSend($domain, exchange, $address, $amount);
        }
        $order_id = insertRowAndGetId(orders, [address => $address, domain => $domain, is_sell => $is_sell, price => $price, amount => $amount, filled => $amount - $not_filled, status => $not_filled == 0 ? 1 : 0, timestamp => $timestamp]);
        // analytics
    }
    return $order_id;
}
