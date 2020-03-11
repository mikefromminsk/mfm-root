<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$domains = get("domains");
$servers = get("servers");

$domain_name = get("domain_name");
$domain_key = get("domain_key");
$server_host_name = get("server_host_name");
if ($domain_name != null && $domain_key != null && $server_host_name != null) {
    $domain = domain_get($domain_name);
    $domain["domain_prev_key"] = $domain_key;
    $domain["domain_key_hash"] = hash(HASH_ALGO, hash(HASH_ALGO, $domain_key) . $domain["server_repo_hash"]);
    $domains = [$domain];
    $servers = [array(
        "server_group_id" => $domain["server_group_id"],
        "server_host_name" => $server_host_name,
    )];
}

if ($domains == null)
    error("domains are null");

$group_assoc = array();
foreach ($domains as $domain) {
    $server_group_id = domain_set($domain["domain_name"], $domain["domain_prev_key"], $domain["domain_key_hash"], $domain["server_repo_hash"]);
    if ($server_group_id !== false)
        $group_assoc[$domain["server_group_id"]] = $server_group_id;
}

echo json_encode($group_assoc);
foreach ($servers as $server) {
    $server_group_id = $group_assoc[$server["server_group_id"]];
    if ($server_group_id != null) {
        if (scalar("select count(*) from servers where server_group_id = $server_group_id "
                . " and server_host_name = '" . uencode($server["server_host_name"]) . "'") == 0) {
            insertList("servers", array(
                "server_group_id" => $server_group_id,
                "server_host_name" => $server["server_host_name"],
                "server_set_time" => time(),
            ));
        }
    }
}
/*
foreach ($group_assoc as $key => $server_group_id) {
    $self_server_repo_hash = scalar("select server_repo_hash from servers where server_group_id = $server_group_id "
        . " and server_host_name = '" . uencode($host_name) . "'");
    $domain_server_repo_hash = scalar("select * from domains where server_group_id = $server_group_id order by domain_set_time desc limit 1");
    if ($self_server_repo_hash != $domain_server_repo_hash) {
        $server_host_name = scalar("select server_host_name from servers "
            . " where server_group_id = $server_group_id and server_repo_hash = '" . uencode($domain_server_repo_hash) . "' limit 1");
        $repo = http_json_post($server_host_name . "/node/file_get.php", array(
            "file_hash" => $domain_server_repo_hash
        ), array("Accept" => "application/repo"));
        if (hash(HASH_ALGO, json_encode($repo)) == $domain_server_repo_hash) {
            domain_repo_set($server_group_id, $repo);
            update("update servers set server_repo_hash = '" . uencode($domain_server_repo_hash) . "', server_set_time = " . time()
                . " where server_group_id = $server_group_id and server_host_name = '" . uencode($host_name) . "' ");
        }
    }
}*/
