<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/properties.php";

$keys = get_required("keys");
$domain_name = get_required_uppercase("domain_name");
$domain_postfix_length = get_int_required("domain_postfix_length");

description("hosting coin");


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

$hosting_time = 123;
$payment = selectRowWhere("payments", array(
    "domain_name" => $domain_name,
    "domain_postfix_length" => $domain_postfix_length,
));
if ($payment == null) {
    insertRow("payments", array(
        "hosting_time" => $hosting_time,
        "domain_name" => $domain_name,
        "domain_postfix_length" => $domain_postfix_length,
    ));
} else {
    updateWhere("payments", array(
        "hosting_time" => $hosting_time,
    ), array(
        "domain_name" => $domain_name,
        "domain_postfix_length" => $domain_postfix_length,
    ));
}


$response["added"] = 0;
for ($i = 0; $i < pow(10, $domain_postfix_length); $i++) {
    $new_domain = $domain_name . sprintf("%0" . $domain_postfix_length . "d", $i);
    $response["added"] += domain_set($host_name, $new_domain, null, null, null);
}

echo json_encode($response);

