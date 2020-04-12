<?php
include_once "domain_utils.php";

$server_host_name = get_required("server_host_name");
$domains = get_required("domains");
$servers = get("servers");

if ($domains == null)
    error("domains are null");

//set domains
$success_domains = domains_set($server_host_name, $domains, $servers);

//download last version
foreach ($success_domains as $domain_name) {
    upgrade($domain_name);
}
