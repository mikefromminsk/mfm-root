<?php

include_once "domain_utils.php";

$back_user_login = get("back_user_login");
$coin_code = get("coin_code");
$domain_keys = get("domain_keys");

$user_id = scalar("select user_id from users where user_login = '$back_user_login'");
$success_domain_names = receive_domain_keys($user_id, $coin_code, $domain_keys);

echo json_encode($success_domain_names);



