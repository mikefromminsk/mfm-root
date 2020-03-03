<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/darkcoin/api/login.php";

$have_coin_code = get_required("have_coin_code");
//$have_coin_count = get_int_required("have_coin_count");
$want_coin_code = get_required("want_coin_code");
$want_coin_count = get_int_required("want_coin_count");

$back_user_login = get_required("back_user_login");
$back_script_url = get_required("back_script_url");
$have_domains = get_required("have_domains");

foreach ($have_domains as $key => $domain)
    $have_domains[$key]["user_id"] = $user_id;

$have_coin_count = sizeof(domains_set($have_coin_code, $have_domains));

$have_coin_code = strtoupper($have_coin_code);
$want_coin_code = strtoupper($want_coin_code);
if ($have_coin_count <= 0)
    error("have_coin_count is zero or less zero");
if ($want_coin_count <= 0)
    error("want_coin_count is zero or less zero");
if ($have_coin_code == $want_coin_code)
    error("you cannot buy and sale the same coins");
$offer_rate = $have_coin_count / $want_coin_count;
$offer_rate_inverse = $want_coin_count / $have_coin_count;


$offer = array(
    "user_id" => $user_id,
    "have_coin_code" => $have_coin_code,
    "have_coin_count" => $have_coin_count,
    "want_coin_code" => $want_coin_code,
    "want_coin_count" => $want_coin_count,
    "start_have_coin_count" => $have_coin_count,
    "start_want_coin_count" => $want_coin_count,
    "back_script_url" => $back_script_url,
    "back_user_login" => $back_user_login,
    "offer_rate" => $offer_rate,
    "offer_rate_inverse" => $offer_rate_inverse,
    "offer_progress" => 0,
);

// auto exchange
$opposite_offers = select("select * from offers where have_coin_code = '$want_coin_code' and  want_coin_code = '$have_coin_code' "
    . " and offer_rate >= $offer_rate_inverse order by offer_rate_inverse limit 20");


foreach ($opposite_offers as $opposite_offer) {

    $offer_have_exchange_coin_count = min($offer["have_coin_count"], $opposite_offer["want_coin_count"]);
    $opposite_have_exchange_coin_count = ceil($offer_have_exchange_coin_count * $opposite_offer["offer_rate"]);


    http_json_post($offer["back_script_url"], array(
        "domain_name" => $opposite_offer["have_coin_code"],
        "domains" => getListFromStart($opposite_offer["have_coin_code"], $opposite_have_exchange_coin_count, $opposite_offer["user_id"], $offer["back_user_login"])
    ));

    http_json_post($opposite_offer["back_script_url"], array(
        "domain_name" => $offer["have_coin_code"],
        "domains" => getListFromStart($offer["have_coin_code"], $offer_have_exchange_coin_count, $offer["user_id"], $opposite_offer["back_user_login"])
    ));

    $opposite_offer["have_coin_count"] = $opposite_offer["have_coin_count"] - $opposite_have_exchange_coin_count;
    $opposite_offer["want_coin_count"] = $opposite_offer["want_coin_count"] - $offer_have_exchange_coin_count;
    if ($opposite_offer["have_coin_count"] != 0 && $opposite_offer["want_coin_count"] != 0) {
        updateList("offers", array(
            "have_coin_count" => $opposite_offer["have_coin_count"],
            "want_coin_count" => $opposite_offer["want_coin_count"],
            "offer_rate" => $opposite_offer["have_coin_count"] / $opposite_offer["want_coin_count"],
            "offer_rate_inverse" => $opposite_offer["want_coin_count"] / $opposite_offer["have_coin_count"],
        ), "offer_id", $opposite_offer["offer_id"]);
    } else {
        if ($opposite_offer["have_coin_count"] > 0) {
            http_json_post($opposite_offer["back_script_url"], array(
                "domain_name" => $opposite_offer["have_coin_code"],
                "domains" => getListFromStart($opposite_offer["have_coin_code"], $opposite_offer["have_coin_count"], $opposite_offer["user_id"], $opposite_offer["back_user_login"]),
            ));
        }
        query("delete from offers where offer_id = " . $opposite_offer["offer_id"]);
    }

    $offer["have_coin_count"] = $offer["have_coin_count"] - $offer_have_exchange_coin_count;
    $offer["want_coin_count"] = $offer["want_coin_count"] - $opposite_have_exchange_coin_count;
    if ($offer["have_coin_count"] != 0 && $offer["want_coin_count"] != 0) {
        $offer["offer_rate"] = $offer["have_coin_count"] / $offer["want_coin_count"];
        $offer["offer_rate_inverse"] = $offer["want_coin_count"] / $offer["have_coin_count"];
    } else {
        // money back
        if ($offer["have_coin_count"] > 0) {
            http_json_post($offer["back_script_url"], array(
                "domain_name" => $offer["have_coin_code"],
                "domains" => getListFromStart($offer["have_coin_code"], $offer["have_coin_count"], $offer["user_id"], $offer["back_user_login"]),
            ));
        }
        break;
    }
}

if ($offer["have_coin_count"] > 0)
    insertList("offers", $offer);


