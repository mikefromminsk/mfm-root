<?php

include_once "login.php";

echo json_encode(array(
    "exchange_server_script" =>  $stock_api_url . "stock.php",
    "stock_fee_in_rub" =>  $stock_fee_in_rub,
    "coins" => selectList("select coin_code from coins"),
    "have_coins" => select("select t1.coin_code, t2.coin_name, count(*) as coin_count from domain_keys t1 "
        . " left join coins t2 on t1.coin_code = t2.coin_code "
        . " where user_id = $user_id group by coin_code"),
));