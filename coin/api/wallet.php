<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/darkcoin/api/login.php";

echo json_encode(array(
    "stock_script" => $stock_url . "darkcoin/api/stock.php",
    "stock_fee_in_rub" => $stock_fee_in_rub,
    "coins" => selectList("select server_domain_name as coin_code from servers where server_url = '" . uencode($server_url) . "'"),
    "have_coins" => select("select t2.server_domain_name as coin_code, count(*) as coin_count from domains t1 "
        . " left join servers t2 on t1.server_group_id = t2.server_group_id and t2.server_url = '" . uencode($server_url) . "'"
        . " where t1.user_id = $user_id group by t1.server_group_id"),
));