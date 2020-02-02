<?php
include_once "domain_utils.php";

$have_coin_code = get_required("have_coin_code");
$want_coin_code = get_required("want_coin_code");
$message = null;

$rates = [
    array("coin_code" => "BTC", "buy_rate"=> 4000, "sale_rate"=> 200),
];//select("select *, max(offer_rate), min(offer_rate) from offers group by have_coin_code");

$have_offers = [];
$buy_offers = select("select * from offers where have_coin_code = '$have_coin_code' and want_coin_code = '$want_coin_code' order by offer_rate limit 20");
$sale_offers = select("select * from offers where have_coin_code = '$want_coin_code' and want_coin_code = '$have_coin_code' order by offer_rate desc limit 20");

echo json_encode(array(
    "message" => $message,
    "buy_offers" => $buy_offers,
    "sale_offers" => $sale_offers,
    "have_offers" => $have_offers,
    "rates" => $rates,
));