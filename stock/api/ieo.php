<?php
include_once "utils.php";

$coins = selectWhere(coins, [type => IEO]);

$ieo = [];
foreach ($coins as $coin) {
    $order = selectRowWhere(orders, [ticker => $coin[ticker], is_sell => 1]);
    $ieo[] = array_merge($coin, $order);
}

$response[ieo] = array_to_map($ieo, ticker);

echo json_encode($response);