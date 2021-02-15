<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/properties.php";

$domain_name = get_required("domain_name");
$domain_postfix_length = get_required("domain_postfix_length");
$payment_keys = get_required("payment_keys");


$payment_tariff = $tariffs[pow(10, $domain_postfix_length)];

if (sizeof($payment_keys) != $payment_tariff) error("dont enough pot");

$new_keys = array();
$payment_domains = [];
foreach ($payment_keys as $domain_name => $domain_key) {
    $new_keys[$domain_name] = random_id();
    $payment_domains[] = array(
        "domain_name" => $domain_name,
        "domain_prev_key" => $domain_key,
        "domain_key_hash" => hash_sha56($new_keys[$domain_name]),
        "server_repo_hash" => null,
    );
}
$payment_domains = domains_set($host_name, $payment_domains);

$payment_expire_time = time() + 1000 * 60 * 60 * 24 * 30;

insertRow("payments", array(
    "payment_host" => "UNKNOWN",
    "payment_currency" => "POT",
    "payment_amount" => sizeof($payment_domains),
    "domain_name" => $domain_name,
    "domain_postfix_length" => $domain_postfix_length,
    "payment_create_time" => time(),
    "payment_payment_time" => time(),
    "payment_expire_time" => $payment_expire_time
));

if (sizeof($payment_domains) != sizeof($new_keys)) error("not all keys are valid");



