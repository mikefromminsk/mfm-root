<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/utils.php";

$login = get_required("login");
$password = get_required("password");

description("user registration");

$token = random_id();

dataPut("users", $login, $password, $token);
dataPut("tokens", $token, $admin_password, $login);

$response["token"] = $token;
$response["login"] = dataGet("tokens", $token, $admin_password);

echo  json_encode($response);



