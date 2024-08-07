<?php

include_once "utils.php";

$count = 6; // get_int(count, 6);
$ticker = get_required_uppercase(ticker);

$response[coin] = selectRowWhere(coins, [ticker => $ticker]);

function getPriceLevels($ticker, $is_sell, $count){
    $levels = select("select price, sum(amount) as amount from orders "
        ." where ticker = '$ticker' and is_sell = $is_sell and status = 0"
        ." group by price order by price DESC limit $count");
    $sum = array_sum(array_column($levels, 'amount'));
    if ($is_sell == 1)
        $levels = array_reverse($levels);
    $accumulate_amount = 0;
    foreach ($levels as &$level){
        $accumulate_amount += $level[amount];
        $level[percent] = $accumulate_amount / $sum * 100;
    }
    if ($is_sell == 1)
        $levels = array_reverse($levels);
    return $levels;
}
$response[sell] = getPriceLevels($ticker, 1, $count);
$response[buy] = getPriceLevels($ticker, 0, $count);

echo json_encode($response);