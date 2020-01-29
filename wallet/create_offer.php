<?php

include_once "login.php";
include_once "domain_utils.php";

$have_coin_code = get_required("have_coin_code");
$have_coin_count = get_int_required("have_coin_count");
$want_coin_code = get_required("want_coin_code");
$want_coin_count = get_int_required("want_coin_count");
$back_url = get_required("back_url");
$offer_domain_keys = get_required("have_domain_keys");
$offer_rate = $have_coin_count / $want_coin_count;
$offer_rate_inverse = $want_coin_count / $have_coin_count;

$offer = array(
    "user_id" => $user_id,
    "have_coin_code" => $have_coin_code,
    "have_coin_count" => $have_coin_count,
    "want_coin_code" => $want_coin_code,
    "want_coin_count" => $want_coin_count,
    "back_url" => $back_url,
    "offer_rate" => $offer_rate,
    "offer_rate_inverse" => $offer_rate_inverse,
);

// set domains
$success_domain_names = receive_domain_keys($user_id, $have_coin_code, $offer_domain_keys);
$result["receive_domain_keys_success"] = sizeof($success_domain_names);
$result["user_have_domain_keys"] = scalar("select count(*) from domain_keys where user_id = $user_id");

// auto exchange
$opposite_offers = select("select * from offers where have_coin_code = '$want_coin_code' and  want_coin_code = '$have_coin_code' "
    . " and offer_rate_inverse >= $offer_rate order by offer_rate_inverse desc, offer_time desc limit 5");

function getDomainKeys($user_id, $have_coin_code, $have_coin_count)
{
    $where = "where user_id = $user_id and coin_code = '$have_coin_code' limit $have_coin_count";
    $domain_keys = select("select domain_name, domain_next_name from domain_keys " . $where);
    query("delete from domain_keys " . $where);
    return $domain_keys;
}

foreach ($opposite_offers as $opposite_offer) {
    $offer_have_exchange_coin_count = min($offer["have_coin_count"], $opposite_offer["want_coin_count"]);
    $opposite_have_exchange_coin_count = ceil($offer_have_exchange_coin_count * $opposite_offer["offer_rate"]);

    $offer_domain_keys = getDomainKeys($offer["user_id"], $offer["have_coin_code"], $offer_have_exchange_coin_count);
    $opposite_domain_keys = getDomainKeys($opposite_offer["user_id"], $opposite_offer["have_coin_code"], $opposite_have_exchange_coin_count);

    http_json_post($offer["back_url"], array(
        "stock_token" => $user["user_session_token"],
        "coin_code" => $opposite_offer["have_coin_code"],
        "domain_keys" => $opposite_domain_keys,
    ));

    http_json_post($opposite_offer["back_url"], array(
        "stock_token" => scalar("select user_session_token from users where user_id = " . $opposite_offer["user_id"]),
        "coin_code" => $offer["have_coin_code"],
        "domain_keys" => $offer_domain_keys,
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
        if ($offer["have_coin_count"] > 0) {
            http_json_post($opposite_offer["back_url"], array(
                "stock_token" => $user["user_session_token"],
                "coin_code" => $opposite_offer["have_coin_code"],
                "domain_keys" => getDomainKeys($opposite_offer["user_id"], $opposite_offer["have_coin_code"], $opposite_offer["have_coin_count"]),
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
            http_json_post($offer["back_url"], array(
                "stock_token" => $user["user_session_token"],
                "coin_code" => $offer["have_coin_code"],
                "domain_keys" => getDomainKeys($offer["user_id"], $offer["have_coin_code"], $offer["have_coin_count"]),
            ));
        }
        break;
    }
}

if ($offer["have_coin_count"] > 0) {
    $result["inserted"] = insertList("offers", array(
        "offer_time" => time(),
        "user_id" => $user_id,
        "have_coin_code" => $have_coin_code,
        "have_coin_count" => $offer["have_coin_count"],
        "want_coin_code" => $want_coin_code,
        "want_coin_count" => $offer["want_coin_count"],
        "offer_rate" => $offer_rate,
        "offer_rate_inverse" => $offer_rate_inverse,
        "back_url" => $back_url,
    ));
}

echo json_encode($result);



