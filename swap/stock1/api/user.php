<?php

include_once "auth.php";

$response[user] = selectRowWhere(users, [user_id => $user_id]);
$response[balances] = array_to_map(selectWhere(balances, [user_id => $user_id]), ticker);
$response[result] = true;
$response[version] = "0.2";

echo json_encode($response);