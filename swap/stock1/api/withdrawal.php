<?php

include_once "auth.php";

$ticker = get_string_required(ticker);
$amount = get_int_required(amount);

$coin = selectRowWhere(coins, [ticker => $ticker, type => ACTIVE]);

checkBalance($user_id, $ticker, $amount);

$response[version] = 0.1;
$response[domain] = $coin[domain];

$response[keys] = select("select * from `keys` where domain_id = $coin[domain_id] and archived = 0 limit $amount");

update("update `keys` set archived = 1  where domain_id = $coin[domain_id] and archived = 0 limit $amount");

transfer(WITHDRAWAL, $user_id, -1, $ticker, $amount);

echo json_encode($response);