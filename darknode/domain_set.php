<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/darknode/domain_utils.php";

$domain_name = get_required("domain_name");
$domains = get("domains");

if ($domains == null) {
    $domain_next_key = random_id();
    $domains = [array(
        "domain_name" => $domain_name,
        "domain_next_key_hash" => hash("sha256", $domain_next_key),
        "domain_next_key" => $domain_next_key,
        "user_login" => $user["user_login"],
    )];
}

domains_set($domain_name, $domains);
