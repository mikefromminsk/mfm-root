<?php

include_once "utils.php";

$ticker = get_required_uppercase(ticker);
$start = get_int_required(start);

$coin = selectRowWhere(coins, [ticker => $ticker, type => ACTIVE]);
$tc = selectRowWhere(tc, [ticker => $ticker, start => $start]);

$winners = tcWinners($ticker, $start);

$all_traded = 0;
$user_traded = 0;
foreach ($winners as $winner)
    $all_traded += $winner[traded];


$rewarded = 0;
foreach ($winners as $winner){
    $reward = round($tc[reward] * $winner[traded] / $all_traded, 2);
    transfer($coin[tc_user_id], $winner[winner], $ticker, $reward);
    $rewarded += $reward;
}

$response[result] = updateWhere(tc, [rewarded => $rewarded], [ticker => $ticker, start => $start]);

echo json_encode($response);