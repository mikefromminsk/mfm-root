<?php

include_once "auth.php";

$response[user] = selectRowWhere(users, [user_id => $user_id]);
$response[balances] = array_to_map(selectWhere(balances, [user_id => $user_id]), ticker);

foreach ($response[balances] as &$balance) {
    $balance[spot] = doubleval($balance[spot]);
    $balance[blocked] = doubleval($balance[blocked]);
}


echo json_encode($response);