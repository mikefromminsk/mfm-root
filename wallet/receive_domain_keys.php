<?php

include_once "login.php";
include_once "domain_utils.php";

$coin_code = get("coin_code");
$domain_keys = get("domain_keys");

$success_domain_names = receive_domain_keys($user_id, $coin_code, $domain_keys);

echo json_encode($success_domain_names);



