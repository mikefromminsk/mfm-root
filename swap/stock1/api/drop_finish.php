<?php

include_once "auth.php";

$drop_id = get_int_required(drop_id);

$is_rewarded = selectWhere(transfers, [type => DROP, parameter => $drop_id]);
$drop = selectRowWhere(drops, [$drop_id => $drop_id]);
$coin = selectRowWhere(coins, [ticker => $drop[ticker], type => ACTIVE]);

if ($drop == null) error("drop $drop_id is not defined");
if ($coin == null) error("$coin[ticker] is not active");
if ($is_rewarded != null) error("reward was received");
if ($drop[rewarded] + $drop[reward] > $drop[total]) error("reward finished");

$drop = selectRowWhere(drops, [$drop_id => $drop_id]);

if ($drop[type] == SIMPLE) {
    $response[result] = transfer(DROP, $coin[drop_user_id], $user_id, $drop[ticker], $drop[reward], $drop[drop_id]);
}

echo json_encode($response);