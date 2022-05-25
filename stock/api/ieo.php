<?php
include_once "utils.php";

$coins = select("select * from coins order by created desc limit 20");

foreach ($coins as &$coin) {
    $coin = array_merge($coin, selectRow("select price * amount as total from orders where ticker = '$coin[ticker]' and user_id = $coin[ieo_user_id]"));
    $coin = array_merge($coin, selectRow("select count(*) as backers, COALESCE(sum(total),0) as founded from trades where taker = $coin[ieo_user_id]"));
    $coin[days_to_go] = $coin[status] == 1 ? 0 : max(0, 30 - round((time() - $coin[created]) / (60 * 60 * 24)));
}

$response[ieo] = $coins;
echo json_encode($response);