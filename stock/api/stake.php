<?php

include_once "auth.php";

$ticker = get_required_uppercase(ticker);
$amount = get_int_required(amount);

$response[result] = stake($user_id, $ticker, $amount);

echo json_encode($response);