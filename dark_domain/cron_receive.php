<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";

$server_host_name = get_required("server_host_name");
$domains = get_required("domains");
$servers = get("servers");

description("cron_receive");

$response_domains = domains_set($server_host_name, $domains, $servers);

echo json_encode(array(
    "server_host_name" => $host_name,
    "domains" => $response_domains,
    "servers" => servers(array_unique(array_column($response_domains, "domain_name"))),
));
