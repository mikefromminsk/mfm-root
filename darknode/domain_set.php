<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db-utils/db.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/darknode/domain_utils.php";

$domain_name = get("domain_name");
$domains = get("domains");

$current_server = selectMap("select * from servers where server_domain_name = '" . uencode($domain_name) . "' and server_url = '" . uencode($server_url) . "'");
if ($current_server == null) {
    insertList("servers", array(
        "server_group_id" => random_id(),
        "server_domain_name" => $domain_name,
        "server_url" => $server_url,
    ));
}

$success = domains_set($domain_name, $domains);

echo json_encode(array( "message" => null));