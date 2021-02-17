<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/properties.php";

$login = get_required("login");

description("user registration");

$token = random_id();

$success = data_put("users.$login.private", array(
    "token" => $token,
));

if (!$success) error("cannot create an account");

data_put("tokens.$token", $admin_token, $login);



