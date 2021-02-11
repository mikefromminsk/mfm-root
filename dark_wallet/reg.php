<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/utils.php";
include_once "properties.php";

$login = get_required("login");
$password_token = get_required("password_token");

description("user registration");

$token = random_id();

$success = data_put("users.$login.private", $password_token, array(
    "token" => $token,
));

if (!$success) error("cannot create account");

data_put("tokens.$token", $admin_token, $login);



