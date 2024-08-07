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