<?php

include_once "utils.php";

$ticker = get_required_uppercase(ticker);
$start = get_int_required(start);

$response[winners] = tcWinners($ticker, $start);

echo json_encode($response);