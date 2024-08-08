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

function incBalance($user_id, $ticker, $amount)
{
    if ($amount != round($amount, 4)) error("inc balance tick error $amount");
    if ($amount == 0) return false;
    $spot = round(getSpot($user_id, $ticker) + $amount, 4);
    $success = updateWhere(balances, [spot => $spot], [user_id => $user_id, ticker => $ticker]);
    if ($success == false)
        return insertRow(balances, [user_id => $user_id, ticker => $ticker, spot => $amount, blocked => 0]);
    return true;
}

function decBalance($user_id, $ticker, $amount)
{
    if ($amount != round($amount, 4)) error("dec balance tick error $amount " . round($amount, 4));
    return incBalance($user_id, $ticker, -$amount);
}

function blkBalance($user_id, $ticker, $amount)
{
    if ($amount != round($amount, 4)) error("blk balance tick error $amount");
    if ($amount == 0) return false;
    $blocked = round(getBlocked($user_id, $ticker) + $amount, 4);
    return updateWhere(balances, [blocked => $blocked], [user_id => $user_id, ticker => $ticker]);
}

function unbBalance($user_id, $ticker, $amount)
{
    if ($amount != round($amount, 4)) error("unb balance tick error $amount");
    return blkBalance($user_id, $ticker, -$amount);
}

function haveBalance($user_id, $ticker, $amount)
{
    if ($amount == 0) return false;
    return getSpot($user_id, $ticker) >= $amount;
}

function checkBalance($user_id, $ticker, $amount)
{
    if (!haveBalance($user_id, $ticker, $amount)) error("not have enough balance");
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

function transfer($type, $from_user_id, $to_user_id, $ticker, $amount, $parameter = null)
{
    if ($type != DEPOSIT && $from_user_id >= 0) {
        if (!haveBalance($from_user_id, $ticker, $amount)) error("donot have enough $ticker for transfer need $amount");
        decBalance($from_user_id, $ticker, $amount);
    }
    if (type != WITHDRAWAL && $to_user_id >= 0){
        incBalance($to_user_id, $ticker, $amount);
    }
    return insertRowAndGetId(transfers, [type => $type, parameter => $parameter, from_user_id => $from_user_id, to_user_id => $to_user_id, ticker => $ticker, amount => $amount, time => time()]);
}

function stake($user_id, $ticker, $amount)
{
    checkBalance($user_id, $ticker, $amount);
    $coin = selectRowWhere(coins, [ticker => $ticker]);
    return transfer(STAKE, $user_id, $coin[staking_user_id], $ticker, $amount, $coin[staking_apy]);
}

function stake_close($user_id, $stake_id)
{
    $stake_transfer = selectRowWhere(transfers, [transfer_id => $stake_id, type => STAKE]);
    $unstake_transfer = selectRowWhere(transfers, [transfer_id => $stake_id, type => UNSTAKE, parameter => $stake_transfer[transfer_id]]);
    $coin = selectRowWhere(coins, [ticker => $stake_transfer[ticker]]);
    if ($stake_transfer == null) error("its not a stake");
    if ($unstake_transfer != null) error("its already unstaked");
    if ($user_id != $stake_transfer[from_user_id]) error("its not yours");
    $unstake_amount = $stake_transfer[amount] + $stake_transfer[amount] * ((time() - $stake_transfer[time]) / (1000 * 60 * 60 * 24 * 365)) * ($stake_transfer[parameter] / 100);
    $unstake_amount = floor($unstake_amount * 100) / 100;
    return transfer(UNSTAKE,  $coin[staking_user_id], $user_id, $coin[ticker], $unstake_amount, $stake_transfer[transfer_id]);
}