<?php

include_once "auth.php";

$ticker = get_required_uppercase(ticker);
$start = get_int_required(start);
$finish = get_int_required(finish);
$reward = get_int(reward);

$coin = selectRowWhere(coins, [ticker => $ticker, type => ACTIVE]);

if ($coin == null) error("$ticker is not active");
if ($coin[user_id] != $user_id) error("you are not admin");
if (!haveBalance($user_id, $ticker, $reward)) error("you donot have enough $ticker");


transfer(TC_START, $user_id, $coin[tc_user_id], $ticker, $reward);

$response[result] = insertRow(tc, [
    ticker => $ticker,
    start => $start,
    finish => $finish,
    reward => $reward,
]);


echo json_encode($response);