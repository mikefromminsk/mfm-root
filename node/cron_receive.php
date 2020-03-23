<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$domains = get("domains");
$servers = get("servers");

if ($domains == null)
    error("domains are null");

//set domains
$success_domains = [];
foreach ($domains as $domain) {
    if (domain_set($domain["domain_name"], $domain["domain_prev_key"], $domain["domain_key_hash"], $domain["server_repo_hash"]) !== false)
        $success_domains[] = $domain["domain_name"];
}

//return last domains
$current_domains = array();
foreach ($domains as $domain) {
    if (!in_array($domain["domain_name"], $success_domains)) {
        $domain_key = scalar("select domain_key from keys where domain_name = " . $domain["domain_name"] . " and domain_key_hash = " . $domain["domain_key_hash"]);
        if ($domain_key != null) {
            $current_domain = domain_get($domain["domain_name"]);
            $current_domain["domain_key"] = $domain_key;
            $current_domains[] = $current_domain;
        }
    }
}
if (sizeof($current_domains) > 0)
    echo json_encode(array(
        "domains" => $current_domains,
        "servers" => servers_get(array_column($current_domains, "domain_name")),
    ));

//set servers
foreach ($servers as $server) {
    /*file_put_contents("sef", json_encode(array(
        "domains" => $domains,
        "servers" => $servers
    )));*/

    if ($server["server_host_name"] != $host_name && in_array($server["domain_name"], $success_domains)) {
        if (scalar("select count(*) from servers "
                . " where domain_name = '" . uencode($server["domain_name"]) . "' "
                . " and server_host_name = '" . uencode($server["server_host_name"]) . "'") == 0) {
            insertList("servers", array(
                "domain_name" => $server["domain_name"],
                "server_host_name" => $server["server_host_name"],
                "server_repo_hash" => $server["server_repo_hash"],
            ));
        } else if ($server["server_repo_hash"] != null) {
            updateList("servers", array(
                "server_repo_hash" => $server["server_repo_hash"]
            ), array(
                "domain_name" => $server["domain_name"],
                "server_host_name" => $server["server_host_name"]
            ));
        }
    }
}

//download last version
foreach ($success_domains as $domain_name) {

    $active_server_repo_hash = domain_get($domain_name)["server_repo_hash"];

    $self_server_repo_hash = scalar("select server_repo_hash from servers "
        . " where domain_name = '" . uencode($domain_name) . "' "
        . " and server_host_name = '" . uencode($host_name) . "'");

    if ($self_server_repo_hash != $active_server_repo_hash) {
        $server_host_name = scalar("select server_host_name from servers "
            . " where domain_name = '" . uencode($domain_name) . "' "
            . " and server_repo_hash = '" . uencode($active_server_repo_hash) . "' limit 1");

        $repo_string = http_get("$server_host_name/$domain_name/app.zip");
        $repo_path = $_SERVER["DOCUMENT_ROOT"] . "/$domain_name/app.zip";
        file_put_contents($repo_path, $repo_string);
        domain_repo_set($domain_name, $repo_path);
    }
}
