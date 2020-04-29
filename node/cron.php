<?php

include_once "domain_utils.php";
foreach (selectList("select distinct server_host_name from servers where server_host_name <> '" . uencode($host_name) . "'")
         as $server_host_name) {

    $start_time = microtime(true);

    $servers = select("select t1.* from servers t1 "
        . " left join domains t2 on t2.domain_name = t1.domain_name "
        . " where t1.server_host_name = '" . uencode($server_host_name) . "'"
        . " and t2.domain_set_time > t1.server_sync_time");

    $domains = array();
    foreach ($servers as $server) {
        $domains = array_merge($domains, select(
            "select * from domains where domain_name = '" . uencode($server["domain_name"]) . "' "
            . " and domain_set_time > " . $server["server_sync_time"]
            . " order by domain_set_time"));
    }

    $request = array(
        "server_host_name" => $host_name,
        "domains" => $domains,
        "servers" => servers(array_column($servers, "domain_name")),
    );
    $response = http_post_json($server_host_name . "/node/cron_receive.php", $request);

    $ping_time = microtime(true) - $start_time;

    domains_set($server_host_name, $response["domains"], $response["servers"]);
}

