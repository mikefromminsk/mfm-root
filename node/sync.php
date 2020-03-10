<?php
/* cron every 5 minutes
dependencies
versions in chain
downloading behavior
roll out auto downloading after 20% approving*/

include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$domains = get("domains");
$servers = get("servers");

if ($domains != null) {
    $group_assoc = array();
    foreach ($domains as $domain) {
        $server_group_id = domain_set($domain["domain_name"], $domain["domain_prev_key"], $domain["domain_key_hash"]);
        if ($server_group_id !== false)
            $group_assoc[$domain["server_group_id"]] = $server_group_id;
    }
    foreach ($servers as $server) {
        $local_server_group_id = $group_assoc[$server["server_group_id"]];
        if (scalar("select count(*) from servers where server_group_id = $local_server_group_id "
                . " and server_host_name = '" . uencode($server["server_host_name"]) . "'") == 0) {
            insertList("servers", array(
                "server_group_id" => $local_server_group_id,
                "server_host_name" => $server["server_host_name"],
                "server_reg_time" => time(),
            ));
        }
    }
} else {
    define("MAX_DOMAIN_COUNT_IN_REQUEST", 1000);
    $server_host_names = selectList("select distinct server_host_name from servers where server_host_name <> '$host_name'");
    foreach ($server_host_names as $remote_host_name) {
        $server_groups = select("select * from servers where server_host_name = '" . uencode($remote_host_name) . "'");
        $server_groups_in_request = [];
        $domains = [];
        foreach ($server_groups as $server_group) {
            $group_domains = select("select * from domains where server_group_id = " . $server_group["server_group_id"]
                . " and domain_set_time > " . $server_group["server_sync_time"]
                . " order by domain_set_time limit " . MAX_DOMAIN_COUNT_IN_REQUEST - sizeof($domains));
            $server_group["max_domain_set_time"] = max(array_column($group_domains, "domain_set_time"));
            $server_groups_in_request[] = $server_group;
            array_merge($domains, $group_domains);
            if (sizeof($domains) == MAX_DOMAIN_COUNT_IN_REQUEST) break;
        }

        $servers = select("select server_group_id, server_host_name from servers "
            . " where server_group_id in (" . implode(",", array_unique(array_column($server_groups_in_request, "server_group_id"))) . ")"
            . " and server_reg_time > " . $server_group["server_sync_time"]);

        if (sizeof($domains) > 0 && http_json_post($remote_host_name . "/node/sync.php", array("domains" => $domains, "servers" => $servers)) !== false) {
            foreach ($server_groups_in_request as $server_group)
                update("update servers set server_sync_time = " . $server_group["max_domain_set_time"]
                    . " where server_group_id = " . $server_group["server_group_id"] . " and server_host_name = '" . uencode($remote_host_name) . "'");
        }
    }
}

