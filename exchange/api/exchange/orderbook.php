<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$count = 6; // get_int(count, 6);
$domain = getDomain();

function getPriceLevels($domain, $is_sell, $count){
    $levels = select("select price, sum(amount) - sum(filled) as amount from orders "
        ." where `domain` = '$domain' and is_sell = $is_sell and status = 0"
        ." group by price order by price DESC limit $count");
    $sum = array_sum(array_column($levels, amount));
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
$response[sell] = getPriceLevels($domain, 1, $count);
$response[buy] = getPriceLevels($domain, 0, $count);

commit($response);