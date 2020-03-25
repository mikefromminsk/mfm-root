<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$domain_name = get("domain_name");
$server_host_name = get("server_host_name");

// reg new hostname
if ($domain_name != null && $server_host_name != null) {
    if (scalar("select count(*) from servers where domain_name = '" . uencode($domain_name) . "' "
            . " and server_host_name = '" . uencode($server_host_name) . "'") == 0) {
        insertList("servers", array(
            "domain_name" => $domain_name,
            "server_host_name" => $server_host_name
        ));
    }
}

foreach (selectList("select distinct server_host_name from servers where server_host_name <> '" . uencode($host_name) . "'") as $server_host_name) {

    $servers = select("select t1.* from servers t1 "
        . " left join domains t2 on t2.domain_name = t1.domain_name "
        . " where t1.server_host_name = '" . uencode($server_host_name) . "'"
        . " and t2.domain_set_time > t1.server_sync_time");

    $domains_in_request = array();
    foreach ($servers as $server) {
        $domains = select("select * from domains where domain_name like '" . uencode($server["domain_name"]) . "%' "
            . " and domain_set_time > " . $server["server_sync_time"]
            . " order by domain_set_time");

        foreach ($domains as &$domain) $domain["domain_name"] = $server["domain_name"];
        $domains_in_request = array_merge($domains_in_request, $domains);
    }
    if (sizeof($domains_in_request) > 0) {

        $request = array(
            "domains" => $domains_in_request,
            "servers" => servers_get(array_column($servers, "domain_name"))
        );

        $start_time = microtime();
        $response = http_json_post($server_host_name . "/node/cron_receive.php", $request);
        $ping_time = microtime() - $start_time;

        if ($response !== false) {
            domains_set($response["domains"], $response["servers"]);
            foreach ($domains_in_request as $domain)
                updateList("servers", array(
                    "server_sync_time" => $domain["domain_set_time"],
                    "server_ping = (server_ping * 300 + $ping_time) / 301"
                ), array(
                    "server_host_name" => $server_host_name,
                    "domain_name" => $domain["domain_name"],
                ));
        }
    }
}

