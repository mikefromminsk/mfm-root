<?php
include_once "domain_utils.php";

$server_host_name = get_required("server_host_name");
$domains = get_required("domains");
$servers = get("servers");

domains_set($server_host_name, $domains, $servers);
// check if this domain was in archive. and show new version
$request = sync_request_data($server_host_name);
file_put_contents("cron_receive.log", json_encode_readable($request));

echo json_encode($request);
