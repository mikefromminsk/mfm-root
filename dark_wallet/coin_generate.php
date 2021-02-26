<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/login.php";

$domain_name = get_required("domain_name");
$domain_postfix_length = get_int_required("domain_postfix_length");

if ($domain_name != "POT")
    include_once "income.php";

description("generate coin");

$user_keys = array();
$domains = array();
for ($i = 0; $i < pow(10, $domain_postfix_length); $i++) {
    $new_domain = $domain_name . sprintf("%0" . $domain_postfix_length . "d", $i);
    $user_keys[$new_domain] = random_id();
    $res = domain_set($host_name, $new_domain, null, hash_sha56($user_keys[$new_domain]), null);
}

$response["keys"] = $user_keys;

echo json_encode($response);

