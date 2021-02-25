<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";

$payment_id = get_required("payment_id");

updateWhere("payments", array("payment_time", time()), array("payment_id" => $payment_id));

data_put("users.$login.private.payments[]", $token, $payment_id);

redirect("/dark_wallet/servers");







include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/properties.php";

$domain_name = get_required("domain_name");



if ($domain_name != "POT") {

    $domain_postfix_length = get_required("domain_postfix_length");
    $payment_keys = get_required("payment_keys");

    $payment_tariff = $tariffs[pow(10, $domain_postfix_length)];

    if (sizeof($payment_keys) != $payment_tariff) error("dont enough pot");

    $new_keys = array();
    $payment_domains = [];
    foreach ($payment_keys as $name => $key) {
        $new_keys[$name] = random_id();
        $payment_domains[] = array(
            "domain_name" => $name,
            "domain_prev_key" => $key,
            "domain_key_hash" => hash_sha56($new_keys[$name]),
            "server_repo_hash" => null,
        );
    }
    $payment_domains = domains_set($host_name, $payment_domains);

    $payment_expire_time = time() + 60 * 60 * 24 * 30;

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
}


