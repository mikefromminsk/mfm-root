<?php
include_once "login.php";
include_once "domain_utils.php";

$have_coin_code = get_required("have_coin_code");
$want_coin_code = get_required("want_coin_code");
$message = null;

$rates = select("select want_coin_code as coin_code, max(offer_rate) as offer_rate from offers "
    . " where have_coin_code = 'USD' group by want_coin_code");
$have_offers = select("select *, floor((start_have_coin_count - have_coin_count) / start_have_coin_count * 100) as offer_progress from offers "
    . " where user_id = $user_id");
$sale_offers = select("select * from offers "
    . " where have_coin_code = '$have_coin_code' and want_coin_code = '$want_coin_code' order by offer_rate_inverse limit 20");
$buy_offers = select("select * from offers "
    . " where have_coin_code = '$want_coin_code' and want_coin_code = '$have_coin_code' order by offer_rate limit 20");

echo json_encode(array(
    "message" => $message,
    "have_offers" => $have_offers,
    "buy_offers" => $buy_offers,
    "sale_offers" => $sale_offers,
    "rates" => $rates,
));