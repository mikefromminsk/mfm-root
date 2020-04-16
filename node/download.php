<?php

include_once "domain_utils.php";

$domain_name = get("domain_name");
$server_host_name = get("server_host_name");

if ($domain_name != null && $server_host_name != null && $server_host_name != $host_name
    && scalarWhere("servers", "count(*)", array("domain_name" => $domain_name, "server_host_name" => $server_host_name)) == 0) {
    insertRow("domains", array("domain_name" => $domain_name, "domain_set_time" => microtime(true)));
    insertRow("servers", array("domain_name" => $domain_name, "server_host_name" => $server_host_name));
}

include_once "cron.php";