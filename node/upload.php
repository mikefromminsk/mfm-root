<?php

include_once "domain_utils.php";

$domain_name = get_required("domain_name");
$domain_key = get_required("domain_key");
$domain_next_key = get_required("domain_next_key");

$result = domain_set($server_host_name, array(
    "domain_name" =>$domain_name,
    "domain_prev_key" => $domain_key,
    "domain_key_hash" => hash_sha56($domain_next_key),
    "server_repo_hash" => "123",
));

echo json_encode($result);