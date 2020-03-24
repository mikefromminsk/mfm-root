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

//return last domains
$current_domains = array();
foreach ($domains as $domain) {
    if (!in_array($domain["domain_name"], $success_domains)) {
        $domain_key = scalar("select domain_key from domain_keys where domain_name = '" . uencode($domain["domain_name"]) . "' "
            . " and domain_key_hash = '" . uencode($domain["domain_key_hash"]) . "'");
        if ($domain_key != null) {
            $current_domain = domain_get($domain["domain_name"]);
            $current_domain["domain_prev_key"] = $domain_key;
            $current_domains[] = $current_domain;
        }
    }
}
file_put_contents("receive", json_encode(array(
    "domains" => $domains,
    "current_domains" => $current_domains,
    "success_domains" => $success_domains,
)));

if (sizeof($current_domains) > 0)
    echo json_encode(array(
        "domains" => $current_domains,
        "servers" => servers_get(array_column($current_domains, "domain_name")),
    ));

foreach (selectList("select distinct server_host_name from servers where server_host_name <> '" . uencode($host_name) . "'") as $server_host_name) {

    $servers = select("select t1.* from servers t1 "
        . " left join domains t2 on t2.domain_name = t1.domain_name "
        . " where t1.server_host_name = '" . uencode($server_host_name) . "'"
        . " and t2.domain_set_time > t1.server_sync_time");

    $domains_in_request = array();
    foreach ($servers as $server) {
        $domains = select("select * from domains where domain_name like '" . uencode($server["domain_name"]) . "%' "
            . " and domain_set_time > " . $server["server_sync_time"]
            . " order by domain_set_time desc");
        foreach ($domains as &$domain) $domain["domain_name"] = $server["domain_name"];
        $domains_in_request = array_merge($domains_in_request, $domains);
    }

    if (sizeof($domains_in_request) > 0) {

        $request = array(
            "domains" => $domains_in_request,
            "servers" => servers_get(array_column($servers, "domain_name"))
        );

        $response = http_json_post($server_host_name . "/node/cron_receive.php", $request);

        if ($response !== false) {
            domains_set($response["domains"], $response["servers"]);
            foreach ($domains_in_request as $domain)
                update("update servers set server_sync_time = " . $domain["domain_set_time"]
                    . " where domain_name = '" . uencode($domain["domain_name"]) . "' and server_host_name = '" . uencode($server_host_name) . "'");
        }
    }
}

