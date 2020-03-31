<?php
include_once "domain_utils.php";


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

        $start_time = microtime();
        $response = http_json_post($server_host_name . "/node/cron_receive.php", array(
            "domains" => $domains_in_request,
            "servers" => select("select * from servers where domain_name in ('" . implode("','", array_column($servers, "domain_name")) . "')")
        ));
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

