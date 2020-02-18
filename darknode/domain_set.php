<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db-utils/db.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/darknode/domain_utils.php";

$usd_domains = get("usd_domains");
$domain_name = get("domain_name");
$domains = get("domains");
$servers = get("servers");

$message = null;

$success_updated_usd = [];
if ($domain_name != "USD") {

    $usd_server_item = selectMap("select * from servers where server_domain_name = 'USD' and server_url = '" . uencode($server_url) . "'");

    if ($usd_server_item == null)
        insertList("servers", array(
            "server_group_id" => 0,
            "server_domain_name" => "USD",
            "server_url" => $server_url,
        ));

    $success_updated_usd = domains_set("USD", $usd_domains);
}


$current_server = selectMap("select * from servers where server_domain_name = '" . uencode($domain_name) . "' and server_url = '" . uencode($server_url) . "'");

if ($current_server != null) {
    update("update servers set server_domain_remove_time = server_domain_remove_time + " . ($hosting_minutes_for_one_usd * 1000 * 60 * sizeof($success_updated_usd))
        . " where server_domain_name = '" . uencode($domain_name) . "' and server_url = '" . uencode($server_url) . "'");
} else {
    insertList("servers", array(
        "server_group_id" => random_id(),
        "server_domain_name" => $domain_name,
        "server_url" => $server_url,
        "server_domain_remove_time" => (time() + $hosting_minutes_for_one_usd * 1000 * 60 * sizeof($success_updated_usd)),
    ));
}

$success_domain_changed = domains_set($domain_name, $domains);

echo json_encode(array(
    "message" => $message,
));