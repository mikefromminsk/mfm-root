<?php
include_once "domain_utils.php";

$domain_name = get("domain_name");
$server_host_name = get("server_host_name");

if ($domain_name != null && $server_host_name != null && $server_host_name != $host_name
    && scalar("select count(*) from servers where domain_name = '" . uencode($domain_name) . "' "
        . " and server_host_name = '" . uencode($server_host_name) . "'") == 0) {

    insertList("domains", array("domain_name" => $domain_name, "domain_set_time" => 0));
    insertList("servers", array("domain_name" => $domain_name, "server_host_name" => $server_host_name));
}
echo json_encode(selectList("select distinct server_host_name from servers where server_host_name <> '" . uencode($host_name) . "'"));

foreach (selectList("select distinct server_host_name from servers where server_host_name <> '" . uencode($host_name) . "'")
         as $server_host_name) {

    $start_time = microtime(true);
    $request = sync_request_data($server_host_name);
    echo json_encode($request);
    $response = http_json_post($server_host_name . "/node/cron_receive.php", $request);
    $ping_time = microtime(true) - $start_time;

    domains_set($server_host_name, $response["domains"], $response["servers"]);

    updateList("servers", array(
        "server_sync_time" => $start_time,
        "server_ping = (server_ping * 300 + $ping_time) / 301"
    ), "server_host_name", $server_host_name);
}

