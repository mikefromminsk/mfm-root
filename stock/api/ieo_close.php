<?php

include_once "utils.php";

$ticker = get_required_uppercase(ticker);

$coin = selectRowWhere(coins, [ticker => $ticker]);

//if ($coin[created] > time() - 60 * 60 * 24 * 30) error("ieo is not finished");

$buy_orders = selectWhere(orders, [ticker => $ticker, is_sell => 0]);
$returned = 0;
foreach ($buy_orders as $order) {
    $return_usdt = $order[filled] * $order[price];
    if (transfer(IEO_FAIL, $coin[ieo_user_id], $order[user_id], USDT, $return_usdt) != null)
        $returned = $return_usdt;
}
$response[returned] = $returned;

echo json_encode($response);


