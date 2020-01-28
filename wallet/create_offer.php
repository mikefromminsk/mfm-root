<?php

include_once "login.php";

$have_coin_code = get_required("have_coin_code");
$have_coin_count = get_int_required("have_coin_count");
$want_coin_code = get_required("want_coin_code");
$want_coin_count = get_int_required("want_coin_count");
$back_url = get_required("back_url");
$have_domain_keys = get_required("have_domain_keys");
$offer_rate = $have_coin_count / $want_coin_count;
$offer_rate_inverse = $want_coin_count / $have_coin_count;

// set domains
$success_domain_names = receive_domain_keys($user_id, $have_coin_code, $have_domain_keys);

// auto exchange
$opposite_offers = select("select * from offers where have_coin_code = '$want_coin_code' and  want_coin_code = '$have_coin_code' "
    . " and offer_rate_inverse <= $offer_rate order by offer_rate");

function satisfyOffers($offer, $opposite_offer, $coin_count)
{
    $have_where = " user_id = " . $offer["user_id"] . " and coin_code = '" . $offer["have_coin_code"] . "' limit $coin_count";
    $have_domain_keys = select("select domain_name, domain_next_name from domain_keys where $have_where");
    $opposite_where = " user_id = " . $opposite_offer["user_id"] . " and coin_code = '" . $opposite_offer["have_coin_code"] . "' limit $coin_count";
    $opposite_domain_keys = select("select domain_name, domain_next_name from domain_keys where $opposite_where");

    http_json_post($offer["back_url"], array(
        "stock_token" => scalar("select user_session_token from users where user_id = " . $offer["user_id"]),
        "coin_code" => $offer["have_coin_code"],
        "domain_keys" => $opposite_domain_keys,
    ));
    http_json_post($opposite_offer["back_url"], array(
        "stock_token" => scalar("select user_session_token from users where user_id = " . $opposite_offer["user_id"]),
        "coin_code" => $opposite_offer["have_coin_code"],
        "domain_keys" => $have_domain_keys,
    ));

    query("delete from domain_keys where $have_where");
    query("delete from domain_keys where $opposite_where");

    if ($opposite_offer["have_coin_code"] == $coin_count)
        query("delete from offers where offer_id = " . $opposite_offer["offer_id"]);
}

$offer = array(
    "user_id" => $user_id,
    "have_coin_code" => $have_coin_code,
    "have_coin_count" => $have_coin_count,
    "want_coin_code" => $want_coin_code,
    "want_coin_count" => $want_coin_count,
    "back_url" => $back_url,
);

foreach ($opposite_offers as $opposite_offer) {
    //$min = min($opposite_offer["have_coin_count"], $offer["want_coin_count"]);
    // full satisfy opposite offer
    //satisfyOffers($offer, $opposite_offer, $opposite_offer["want_coin_count"]);
    //$offer["have_coin_count"] -= $opposite_offer["have_coin_count"];
}

if ($offer["have_coin_count"] > 0) {
    $success = insertList("offers", array(
        "offer_time" => time(),
        "user_id" => $user_id,
        "have_coin_code" => $have_coin_code,
        "have_coin_count" => $offer["have_coin_count"],
        "want_coin_code" => $want_coin_code,
        "want_coin_count" => $want_coin_count,
        "offer_rate" => $offer_rate,
        "offer_rate_inverse" => $offer_rate_inverse,
        "back_url" => $back_url,
    ));
}




