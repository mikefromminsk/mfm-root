<?php

include_once "domain_utils.php";

$back_user_login = get_required("back_user_login");
$coin_code = get_required("coin_code");
$domain_keys = get_required("domain_keys");

$user_id = scalar("select user_id from users where user_login = '$back_user_login'");
$success_domain_names = receiveDomainKeys($user_id, $coin_code, $domain_keys);

//send($user["login", ])

echo json_encode(array(
    "message" => sizeof($domain_keys) == sizeof($success_domain_names) ? null : "receive error",
    "success_domain_names" => $success_domain_names
));



