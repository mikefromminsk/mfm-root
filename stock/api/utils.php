<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

function getSpot($user_id, $ticker)
{
    return scalar("select spot from balances where user_id = $user_id and ticker = '$ticker'");
}

function getBlocked($user_id, $ticker)
{
    return scalar("select blocked from balances where user_id = $user_id and ticker = '$ticker'");
}

function incBalance($user_id, $ticker, $coins)
{
    if ($coins != round($coins, 4)) error("inc balance tick error $coins");
    if ($coins == 0) return false;
    $spot = round(getSpot($user_id, $ticker) + $coins, 4);
    $success = updateWhere(balances, [spot => $spot], [user_id => $user_id, ticker => $ticker]);
    if ($success == false)
        return insertRow(balances, [user_id => $user_id, ticker => $ticker, spot => $coins, blocked => 0]);
    return true;
}

function decBalance($user_id, $ticker, $coins)
{
    if ($coins != round($coins, 4)) error("dec balance tick error $coins " . round($coins, 4));
    return incBalance($user_id, $ticker, -$coins);
}

function blkBalance($user_id, $ticker, $coins)
{
    if ($coins != round($coins, 4)) error("blk balance tick error $coins");
    if ($coins == 0) return false;
    $blocked = round(getBlocked($user_id, $ticker) + $coins, 4);
    return updateWhere(balances, [blocked => $blocked], [user_id => $user_id, ticker => $ticker]);
}

function unbBalance($user_id, $ticker, $coins)
{
    if ($coins != round($coins, 4)) error("unb balance tick error $coins");
    return blkBalance($user_id, $ticker, -$coins);
}

function haveBalance($user_id, $ticker, $amount)
{
    if ($amount == 0) return false;
    return getSpot($user_id, $ticker) >= $amount;
}

function place($user_id, $ticker, $is_sell, $price, $amount)
{
    if ($price != round($price, 2)) error("price tick is 0.01");
    if ($amount != round($amount, 2)) error("amount tick is 0.01");
    $total = round($price * $amount, 4);
    $timestamp = time();
    $trade_volume = 0;

    $coin = selectRowWhere(coins, [ticker => $ticker]);
    if ($coin[type] == IEO) {
        if ($coin[price] != 0 && $is_sell == 1) error("only one sell order has to be in ieo");
        if ($coin[created] < time() - 60 * 60 * 24 * 30) error("ieo is finished");
        if ($coin[price] == 0 && $is_sell == 0) error("first ieo order has to be sell");
    }

    if ($is_sell == 1) {
        if (!haveBalance($user_id, $ticker, $amount)) error("not enough balance");
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
                transfer($coin[ieo_user_id], $coin[user_id], $ticker, getSpot($coin[ieo_user_id], $ticker));
                transfer($coin[ieo_user_id], $coin[user_id], USDT, getSpot($coin[ieo_user_id], USDT));
            }
        }
    }

    if ($last_trade_price != null) {
        foreach ([1 * 60, 1 * 60 * 5, 1 * 60 * 15, 1 * 60 * 60, 1 * 60 * 60 * 24] as $seconds) {
            $trade_period = ceil($timestamp / $seconds) * $seconds;
            $last_trade_period = ceil($coin[last_trade_timestamp] / $seconds) * $seconds;
            if ($trade_period == $last_trade_period) {
                update("update sticks set low = LEAST(low, $last_trade_price), high = GREATEST(high, $last_trade_price), close = $last_trade_price, volume = volume + $trade_volume where ticker = '$ticker' and period = $seconds and time = $last_trade_period");
            } else {
                insertRow(sticks, [ticker => $ticker, period => $seconds, time => $trade_period, low => $last_trade_price, high => $last_trade_price, open => $last_trade_price, close => $last_trade_price, volume => $trade_volume]);
            }
        }
        updateWhere(coins, [price => $last_trade_price, last_trade_timestamp => $timestamp], [ticker => $ticker]);
    }
    return $order_id;
}

function cancel($user_id, $order_id)
{
    $order = selectRowWhere(orders, [order_id => $order_id]);
    if ($order == null) error("order not exist");
    if ($order[status] != 0) error("cannot cancel");
    if ($order["user_id"] != $user_id) error("not yours");

    if ($order[is_sell]) {
        $amount = round($order[amount] - $order[filled], 2);
        unbBalance($user_id, $order[ticker], $amount);
        incBalance($user_id, $order[ticker], $amount);
    } else {
        $amount = round(($order[amount] - $order[filled]) * $order[price], 4);
        unbBalance($user_id, USDT, $amount);
        incBalance($user_id, $order[ticker], $amount);
    }
    return updateWhere(orders, [status => -1], [order_id => $order_id]);
}

function cancelAll($user_id, $ticker)
{
    $order_ids = selectListWhere(orders, order_id, [user_id => $user_id, ticker => $ticker, status => 0]);
    foreach ($order_ids as $order_id)
        cancel($user_id, $order_id);
    return true;
}

function createUser($token, $email = null)
{
    $user_id = insertRowAndGetId("users", [token => $token, email => $email]);
    insertRow("balances", [user_id => $user_id, ticker => "USDT", spot => 0, blocked => 0]);
    return $user_id;
}

function transfer($from_user_id, $to_user_id, $ticker, $amount)
{
    if (!haveBalance($from_user_id, $ticker, $amount)) error("donot have enough $ticker for transfer need $amount");
    decBalance($from_user_id, $ticker, $amount);
    incBalance($to_user_id, $ticker, $amount);
    insertRow(transfers, [from_user_id => $from_user_id, to_user_id => $to_user_id, ticker => $ticker, amount => $amount, time => time()]);
    return true;
}

function tcWinners($ticker, $start)
{
    $tc = selectRowWhere(tc, [ticker => $ticker, start => $start]);
    return select("select maker as winner, sum(amount) as traded "
        . " from trades where ticker = '$ticker' and time >= $tc[start] and time <= $tc[finish] "
        . " group by maker order by traded desc");
}