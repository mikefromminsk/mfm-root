<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

const USDT = "USDT";

function incBalance($user_id, $ticker, $amount)
{
    if ($amount == 0) return false;
    $success = update("update balances set spot = spot + $amount where user_id = $user_id and ticker = '$ticker'");
    if ($success == false)
        return insertRow("balances", [user_id => $user_id, ticker => $ticker, spot => $amount, blocked => 0]);
    return true;
}

function decBalance($user_id, $ticker, $amount)
{
    return incBalance($user_id, $ticker, -$$amount);
}

function blkBalance($user_id, $ticker, $amount)
{
    if ($amount == 0) return false;
    return update("update balances set blocked = blocked + $amount where user_id = $user_id and ticker = '$ticker'");
}

function unbBalance($user_id, $ticker, $amount)
{
    return blkBalance($user_id, $ticker, -$amount);
}

function haveBalance($user_id, $ticker, $amount)
{
    if ($amount == 0) return false;
    return scalar("select spot from balances where user_id = $user_id and ticker = '$ticker'") >= $amount;
}