<?php
include_once "domain_utils.php";

$have_coin_code = get_required("have_coin_code");
$want_coin_code = get_required("want_coin_code");

$coins = select("select * from coins");

$buy_offers = select("select * from offers where have_coin_code = '$have_coin_code' and want_coin_code = '$want_coin_code' order by offer_rate limit 20");
$sell_offers = select("select * from offers where have_coin_code = '$want_coin_code' and want_coin_code = '$have_coin_code' order by offer_rate desc limit 20");

echo json_encode(array("message" => $message));