<?php
// coins
// orders
//

function place($user_id, $ticker, $is_sell, $price, $amount)
{
    if ($price != round($price, 2)) error("price tick is 0.01");
    if ($amount != round($amount, 2)) error("amount tick is 0.01");
    $total = round($price * $amount, 4);
    $timestamp = time();
    $trade_volume = 0;

    $coin = selectRowWhere(coins, [ticker => $ticker]);

    if ($is_sell == 1) {
        checkBalance($user_id, $ticker, $amount);
        $not_filled = $amount;
        decBalance($user_id, $ticker, $amount);
        foreach (select("select * from orders where ticker = '$ticker' and is_sell = 0 and price >= $price and status = 0 order by price DESC,timestamp") as $order) {
            $order_not_filled = round($order[amount] - $order[filled], 2);
            $coin_to_fill = min($not_filled, $order_not_filled);
            $usdt_to_fill = round($coin_to_fill * $order[price], 4);
            updateWhere(orders, [filled => $order[filled] + $coin_to_fill, status => $order_not_filled == $coin_to_fill ? 1 : 0], [order_id => $order[order_id]]);
            unbBalance($order[user_id], USDT, $usdt_to_fill);
            incBalance($order[user_id], $ticker, $coin_to_fill);
            incBalance($user_id, USDT, $usdt_to_fill);
            insertRow(trades, [time => time(), ticker => $ticker, is_sell => 1, maker => $user_id, taker => $order[user_id], price => $order[price], amount => $coin_to_fill, total => $usdt_to_fill]);
            $not_filled = round($not_filled - $coin_to_fill, 2);
            $last_trade_price = $order[price];
            $trade_volume += $usdt_to_fill;
            if ($not_filled == 0)
                break;
        }
        blkBalance($user_id, $ticker, $not_filled);
        $order_id = insertRowAndGetId(orders, [user_id => $user_id, ticker => $ticker, is_sell => $is_sell, price => $price, amount => $amount, filled => $amount - $not_filled, status => $not_filled == 0 ? 1 : 0, timestamp => $timestamp]);

        if ($coin[price] == 0) {
            updateWhere(coins, [price => $price], [ticker => $ticker]);
        }
    } else {
        if (!haveBalance($user_id, USDT, $total)) error("not enough balance");
        $not_filled = $amount;
        foreach (select("select * from orders where ticker = '$ticker' and is_sell = 1 and price <= $price and status = 0 order by price,timestamp") as $order) {
            $order_not_filled = round($order[amount] - $order[filled], 2);
            $coin_to_fill = min($not_filled, $order_not_filled);
            $usdt_to_fill = round($coin_to_fill * $order[price], 4);
            updateWhere(orders, [filled => $order[filled] + $coin_to_fill, status => $order_not_filled == $coin_to_fill ? 1 : 0], [order_id => $order[order_id]]);
            unbBalance($order[user_id], $ticker, $coin_to_fill);
            incBalance($order[user_id], USDT, $usdt_to_fill);
            incBalance($user_id, $ticker, $coin_to_fill);
            decBalance($user_id, USDT, $usdt_to_fill);
            insertRow(trades, [time => time(), ticker => $ticker, is_sell => 0, maker => $user_id, taker => $order[user_id], price => $order[price], amount => $coin_to_fill, total => $usdt_to_fill]);
            $not_filled = round($not_filled - $coin_to_fill, 2);
            $last_trade_price = $order[price];
            $trade_volume += $usdt_to_fill;
            if ($not_filled == 0)
                break;
        }
        decBalance($user_id, USDT, round($not_filled * $price, 4));
        blkBalance($user_id, USDT, round($not_filled * $price, 4));
        $order_id = insertRowAndGetId(orders, [user_id => $user_id, ticker => $ticker, is_sell => $is_sell, price => $price, amount => $amount, filled => $amount - $not_filled, status => $not_filled == 0 ? 1 : 0, timestamp => $timestamp]);

        if ($coin[type] == IEO) {
            $sell_order = selectRowWhere(orders, [ticker => $ticker, is_sell => 1]);
            if ($sell_order[status] == 1) {
                updateWhere(coins, [type => ACTIVE], [ticker => $ticker]);
                transfer(TRADE, $coin[ieo_user_id], $coin[user_id], $ticker, getSpot($coin[ieo_user_id], $ticker));
                transfer(TRADE, $coin[ieo_user_id], $coin[user_id], USDT, getSpot($coin[ieo_user_id], USDT));
            }
        }
    }


    if ($last_trade_price != null) {
        foreach ([1, 1 * 60] as $seconds) {
            $trade_period = ceil($timestamp / $seconds) * $seconds;
            $last_trade_period = ceil($coin[last_trade_timestamp] / $seconds) * $seconds;
            /*if ($trade_period == $last_trade_period) {
                update("update candles set low = LEAST(low, $last_trade_price), high = GREATEST(high, $last_trade_price), close = $last_trade_price, volume = volume + $trade_volume "
                    ." where ticker = '$ticker' and period = $seconds and time = $last_trade_period");
            } else */{
                insertRow(candles, [ticker => $ticker, period => $seconds, time => $trade_period,
                    low => min($last_trade_price, $coin[price]), high => max($last_trade_price, $coin[price]),
                    open => $coin[price], close => $last_trade_price,
                    volume => $trade_volume]);
            }
        }
        updateWhere(coins, [price => $last_trade_price, last_trade_timestamp => $timestamp], [ticker => $ticker]);
    }
    return $order_id;
}