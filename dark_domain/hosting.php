<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";

$keys = get_required("keys");
$domain_name = get_required_uppercase("domain_name");
$domain_postfix_length = get_int_required("domain_postfix_length");

description("hosting coin");

/*if ($domain_name != "POT"){
    $new_keys = array();

    $response["apply"] = 0;
    foreach ($keys as $pot_domain_name => $pot_prev_key) {
        $new_keys[$pot_domain_name] = random_id();
        $response["apply"] += domain_set($host_name, $pot_domain_name, $pot_prev_key, hash_sha56($new_keys[$pot_domain_name]), null);
    }

    http_post_json("//dark_wallet/save.php", array(
        "domain_name" => "POT",
        "keys" => $new_keys
    ));
}*/


$hosting_seconds = 1000000;
$payment_expire_time = scalarWhere("hosting", "hosting_expire_time", array(
    "domain_name" => $domain_name,
    "domain_postfix_length" => $domain_postfix_length,
));

$next_expire_time = $payment_expire_time == null || $payment_expire_time < time() ? time() + $hosting_seconds : $payment_expire_time + $hosting_seconds;

if ($payment_expire_time == null) {
    insertRow("hosting", array(
        "domain_name" => $domain_name,
        "domain_postfix_length" => $domain_postfix_length,
        "hosting_expire_time" => $next_expire_time,
    ));

    $response["errors"] = 0;
    for ($i = 0; $i < pow(10, $domain_postfix_length); $i++) {
        $new_domain = $domain_name . sprintf("%0" . $domain_postfix_length . "d", $i);
        $response["errors"] += sizeof(domain_set($host_name, $new_domain, null, null, null));
    }
} else {
    updateWhere("hosting", array(
        "hosting_expire_time" => $next_expire_time,
    ), array(
        "domain_name" => $domain_name,
        "domain_postfix_length" => $domain_postfix_length,
    ));
}


echo json_encode($response);

