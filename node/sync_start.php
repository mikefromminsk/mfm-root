<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$domain_name = get("domain_name");
$server_host_name = get("server_host_name");

if ($domain_name != null && $server_host_name != null) {
    $server_group_id = scalar("select server_group_id from domains where domain_name = '" . uencode($domain_name) . "'");
    if (scalar("select count(*) from servers where server_group_id = $server_group_id and server_host_name = '" . uencode($server_host_name) . "'") == 0)
        insertList("servers", array(
            "server_group_id" => $server_group_id,
            "server_host_name" => $server_host_name,
            "server_set_time" => time()
        ));
}

$server_host_names = selectList("select distinct server_host_name from servers where server_host_name <> '$host_name'");

define("MAX_DOMAIN_COUNT_IN_REQUEST", 1000);

foreach ($server_host_names as $server_host_name) {
    $groups_in_request = [];
    $domains_in_request = [];

    $groups = select("select * from servers where server_host_name = '" . uencode($server_host_name) . "'");

    foreach ($groups as $group) {
        $domains = select("select * from domains where server_group_id = " . $group["server_group_id"]
            . ($group["server_sync_time"] != null ? " and domain_set_time > " . $group["server_sync_time"] : null)
            . " order by domain_set_time limit " . (MAX_DOMAIN_COUNT_IN_REQUEST - sizeof($domains_in_request)));

        $group["max_domain_set_time"] = max(array_column($domains, "domain_set_time"));
        $groups_in_request[] = $group;

        $domains_in_request = array_merge($domains_in_request, $domains);
        if (sizeof($domains_in_request) == MAX_DOMAIN_COUNT_IN_REQUEST) break;
    }

    $servers = [];
    foreach ($groups_in_request as $group) {
        $servers = array_merge($servers, select("select server_group_id, server_host_name, server_repo_hash from servers "
            . " where server_group_id = " . $group["server_group_id"]
            . " and server_host_name <> '" . uencode($server_host_name) . "'"
            . ($group["server_sync_time"] != null ? " and server_set_time > " . $group["server_sync_time"] : "")));
    }

    if (sizeof($domains_in_request) > 0) {
        $request = array(
            "domains" => $domains_in_request,
            "servers" => $servers
        );
        $response = http_json_post($server_host_name . "/node/sync_receive.php", $request);

        if ($response != null && $response !== false) {
            foreach ($groups_in_request as $group) {
                update("update servers set server_sync_time = " . $group["max_domain_set_time"]
                    . " where server_group_id = " . $group["server_group_id"] . " and server_host_name = '" . uencode($server_host_name) . "'");
            }
        }
    }
}

