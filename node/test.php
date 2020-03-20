<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";
$servers = json_decode("[{\"domain_name\":\"search\",\"server_host_name\":\"192.168.200.1\",\"server_repo_hash\":\"32c65b421fcddc9d4274440ec72eafcfe2d03806f98f5bd7550eb8c9b5dff9f0\",\"server_sync_time\":\"0\"},{\"domain_name\":\"search\",\"server_host_name\":\"192.168.200.129\",\"server_repo_hash\":null,\"server_sync_time\":\"0\"}]", true);
echo json_encode($servers);
array_shift($servers);
//set servers
foreach ($servers as $server) {
    insertList("servers", array(
        "domain_name" => $server["domain_name"],
        "server_host_name" => $server["server_host_name"],
        "server_repo_hash" => $server["server_repo_hash"],
    ), true);
}
