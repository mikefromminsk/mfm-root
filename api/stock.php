<?php
include_once "domain_utils.php";

$have_coin_code = get("have_coin_code");
$want_coin_code = get("want_coin_code");
$message = null;

$rates = [
    array("coin_code" => "BTC", "buy_rate"=> 4000, "sale_rate"=> 200),
];//select("select *, max(offer_rate), min(offer_rate) from offers group by have_coin_code");

$have_offers = [];
$buy_offers = select("select * from offers where have_coin_code = '$have_coin_code' and want_coin_code = '$want_coin_code' order by offer_rate limit 20");
$sale_offers = select("select * from offers where have_coin_code = '$want_coin_code' and want_coin_code = '$have_coin_code' order by offer_rate desc limit 20");

$buy_offers = [array(
    "user_id" => $user_id,
    "have_coin_code" => "BTC",
    "have_coin_count" => 12,
    "want_coin_code" => "USD",
    "want_coin_count" => 12,
    "start_have_coin_count" => 12,
    "start_want_coin_count" => 12,
    "back_host_url" => $host_url,
    "back_user_login" => "login",
    "offer_rate" => 12,
    "offer_rate_inverse" => 12,
)];

$sale_offers = $buy_offers;
echo json_encode(array(
    "message" => $message,
    "buy_offers" => $buy_offers,
    "sale_offers" => $sale_offers,
    "have_offers" => $have_offers,
    "rates" => $rates,
));