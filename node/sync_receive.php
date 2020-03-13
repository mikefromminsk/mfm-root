<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$domains = get("domains");
$servers = get("servers");

if ($domains == null)
    error("domains are null");

//set domains
$group_assoc = array();
foreach ($domains as $domain) {
    $server_group_id = domain_set($domain["domain_name"], $domain["domain_prev_key"], $domain["domain_key_hash"], $domain["server_repo_hash"]);
    if ($server_group_id !== false)
        $group_assoc[$domain["server_group_id"]] = $server_group_id;
}
//set servers
foreach ($servers as $server){
    if ($server["server_host_name"] != $host_name && $group_assoc[$server["server_group_id"]] != null) {
        if (scalar("select count(*) from servers "
                . " where server_group_id = " . $group_assoc[$server["server_group_id"]]
                . " and server_host_name = '" . uencode($server["server_host_name"]) . "'") == 0) {
            insertList("servers", array(
                "server_group_id" => $server_group_id,
                "server_repo_hash" => $server["server_repo_hash"],
                "server_host_name" => $server["server_host_name"],
                "server_set_time" => time(),
            ));
        } else if ($server["server_repo_hash"] != null) {
            update("update servers set server_repo_hash = '".uencode($server["server_repo_hash"])."' "
                . " where server_group_id = " . $group_assoc[$server["server_group_id"]]
                . " and server_host_name = '" . uencode($server["server_host_name"]) . "'");
        }
    }
}
//retrace
foreach ($group_assoc as $key => $server_group_id) {
    $self_server_repo_hash = scalar("select server_repo_hash from servers where server_group_id = $server_group_id "
        . " and server_host_name = '" . uencode($host_name) . "'");

    $domain = selectMap("select * from domains where server_group_id = $server_group_id order by domain_set_time desc limit 1");
    if ($self_server_repo_hash != $domain["server_repo_hash"]) {

        $server_host_name = scalar("select server_host_name from servers "
            . " where server_group_id = $server_group_id and server_repo_hash = '" . uencode($domain["server_repo_hash"]) . "' limit 1");

        $repo = http_post($server_host_name . "/node/file_get.php", array(
            "domain_name" => $domain["domain_name"],
        ));


        if (hash(HASH_ALGO, $repo) == $domain["server_repo_hash"]) {
            $repo = json_decode($repo);
            query("delete from files where server_group_id = $server_group_id");
            foreach ($repo as $file_path => $file_data) {
                $hash = hash(HASH_ALGO, $file_data);
                if ($domain["domain_name"] == "node") {
                    file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/node/" . $file_path, $file_data);
                } else {
                    file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/node/files/" . $hash, $file_data);
                }
                insertList("files", array(
                    "server_group_id" => $server_group_id,
                    "file_path" => $file_path,
                    "file_level" => substr_count($file_path, "/"),
                    "file_size" => sizeof($file_data),
                    "file_hash" => $hash,
                ));
            }
            update("update servers set server_repo_hash = '" . uencode($domain["server_repo_hash"]) . "', server_set_time = " . time()
                . " where server_group_id = $server_group_id and server_host_name = '" . uencode($host_name) . "' ");
        }

    }
}
