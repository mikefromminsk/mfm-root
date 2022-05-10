<?php

include_once "auth.php";

$ticker = get_required_uppercase(ticker);
$type = get_required_uppercase(type);
$total = get_int_required(total);
$reward = get_int_required(reward);

$coin = selectRowWhere(coins, [ticker => $ticker, type => ACTIVE]);

if ($coin == null) error("$ticker is not active");
if ($coin[user_id] != $user_id) error("you are not admin");
if (!haveBalance($user_id, $ticker, $reward)) error("you donot have enough $ticker");
if (!in_array($type, [SIMPLE])) error("not supported type");

$drop_id = insertRowAndGetId(drops, [type => $type, ticker => $ticker, reward => $reward, total => $total]);
transfer(DROP_DEPOSIT, $user_id, $coin[drop_user_id], $ticker, $total, $drop_id);
$response[result] = true;



echo json_encode($response);