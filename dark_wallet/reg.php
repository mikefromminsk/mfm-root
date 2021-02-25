<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/properties.php";

$login = get_required("login");
$password = get_required("password");

description("user registration");

$token = random_id();

data_put("users.$login", $password);

data_put("users.$login.token", $password, $token);

$response["token"] = $token;

echo  json_encode($response);



