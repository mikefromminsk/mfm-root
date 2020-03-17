<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$domain_name = get("domain_name");
$server_host_name = get("server_host_name");

// reg new hostname
if ($domain_name != null && $server_host_name != null) {
    if (scalar("select count(*) from servers where domain_name = '" . uencode($domain_name) . "' "
            . " and server_host_name = '" . uencode($server_host_name) . "'") == 0)
        insertList("servers", array(
            "domain_name" => $domain_name,
            "server_host_name" => $server_host_name,
            "server_set_time" => time()
        ));
}

define("MAX_DOMAIN_COUNT_IN_REQUEST", 1000);
$server_host_names = selectList("select distinct server_host_name from servers where server_host_name <> '" . uencode($host_name) . "'");

foreach ($server_host_names as $server_host_name) {

    $domains_in_request = select("select t2.* from servers t1 "
        . " left join domains t2 where t2.domain_name = t1.domain_name "
        . " where t1.server_host_name = '" . uencode($server_host_name) . "'"
        . " and t2.domain_set_time >= t1.server_sync_time");

    $servers_with_domains = select("select * from servers "
        . " where domain_name in ('" . implode("','", array_column($domains_in_request, "domain_name")) . "')");

    if (sizeof($domains_in_request) > 0) {
        $response = http_json_post($server_host_name . "/node/sync_receive.php", array(
            "domains" => $domains_in_request,
            "servers" => $servers_with_domains
        ));
        if ($response !== false) {
            foreach ($domains_in_request as $domain)
                update("update servers set server_sync_time = " . $domain["domain_set_time"]
                    . " where domain_name = '".uencode($domain["domain_name"])."' and server_host_name = '" . uencode($server_host_name) . "'");
        }
    }
}

