<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";

$domain_name = get("domain_name");
$server_host_name = get("server_host_name");

description("download");

if ($server_host_name == $host_name)
    error("server_host_name is invalid");

// if exist
if (scalarWhere("domains", "count(*)", array("domain_name" => $domain_name)) == 0)
    insertRow("domains", array("domain_name" => $domain_name, "domain_set_time" => microtime(true)));
if (scalarWhere("servers", "count(*)", array("domain_name" => $domain_name, "server_host_name" => $server_host_name)) == 0)
    insertRow("servers", array("domain_name" => $domain_name, "server_host_name" => $server_host_name));
if (scalarWhere("servers", "count(*)", array("domain_name" => $domain_name, "server_host_name" => $host_name)) == 0)
    insertRow("servers", array("domain_name" => $domain_name, "server_host_name" => $host_name));

include_once "cron.php";