<?php

include_once "login.php";
include_once "domain_utils.php";

$have_coin_code = get_required("have_coin_code");
$have_coin_count = get_int_required("have_coin_count");
$want_coin_code = get_required("want_coin_code");
$want_coin_count = get_int_required("want_coin_count");
$back_user_login = get_required("back_user_login");
$back_host_url = get_required("back_host_url");
$offer_domain_keys = get_required("have_domain_keys");

$have_coin_code = strtoupper($have_coin_code);
$want_coin_code = strtoupper($want_coin_code);
if ($have_coin_count <= 0)
    db_error(0, "have_coin_count is zero or less zero");
if ($want_coin_count <= 0)
    db_error(0, "want_coin_count is zero or less zero");

$offer_rate = $have_coin_count / $want_coin_count;
$offer_rate_inverse = $want_coin_count / $have_coin_count;
$message = null;

$offer = array(
    "user_id" => $user_id,
    "have_coin_code" => $have_coin_code,
    "have_coin_count" => $have_coin_count,
    "want_coin_code" => $want_coin_code,
    "want_coin_count" => $want_coin_count,
    "start_have_coin_count" => $have_coin_count,
    "start_want_coin_count" => $want_coin_count,
    "back_host_url" => $back_host_url,
    "back_user_login" => $back_user_login,
    "offer_rate" => $offer_rate,
    "offer_rate_inverse" => $offer_rate_inverse,
    "offer_progress" => 0,
);

if ($have_coin_count < 0 || $want_coin_count < 0) {
    $message = "numbers are less zero";
} else {

// set domains
    $success_domain_names = receive_domain_keys($user_id, $have_coin_code, $offer_domain_keys);

// auto exchange
    $opposite_offers = select("select * from offers where have_coin_code = '$want_coin_code' and  want_coin_code = '$have_coin_code' "
        . " and offer_rate_inverse >= $offer_rate order by offer_rate_inverse desc limit 5");


    foreach ($opposite_offers as $opposite_offer) {
        $offer_have_exchange_coin_count = min($offer["have_coin_count"], $opposite_offer["want_coin_count"]);
        $opposite_have_exchange_coin_count = ceil($offer_have_exchange_coin_count * $opposite_offer["offer_rate"]);

        http_json_post($offer["back_host_url"] . "receive_domain_keys", array(
            "back_user_login" => $offer["back_user_login"],
            "coin_code" => $opposite_offer["have_coin_code"],
            "domain_keys" => getDomainKeys($opposite_offer["user_id"], $opposite_offer["have_coin_code"], $opposite_have_exchange_coin_count)
        ));

        http_json_post($opposite_offer["back_host_url"] . "receive_domain_keys", array(
            "back_user_login" => $opposite_offer["back_user_login"],
            "coin_code" => $offer["have_coin_code"],
            "domain_keys" => getDomainKeys($offer["user_id"], $offer["have_coin_code"], $offer_have_exchange_coin_count)
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
                http_json_post($opposite_offer["back_host_url"] . "receive_domain_keys", array(
                    "back_user_login" => $opposite_offer["back_user_login"],
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
                http_json_post($offer["back_host_url"] . "receive_domain_keys", array(
                    "back_user_login" => $offer["back_user_login"],
                    "coin_code" => $offer["have_coin_code"],
                    "domain_keys" => getDomainKeys($offer["user_id"], $offer["have_coin_code"], $offer["have_coin_count"]),
                ));
            }
            break;
        }
    }

    if ($offer["have_coin_count"] > 0)
        insertList("offers", $offer);
}


echo json_encode(array(
    "message" => $message,
    "exchanged" => $have_coin_count - $offer["have_coin_count"]
));


