<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/utils.php";

$login = get_required("login");
$password = get_required("password");

description("user registration");



dataId("users",  $admin_token);
dataId(["tokens"],  $admin_token);


$token = random_id();

dataSet("users", $login, $token, null);
dataSet("tokens", $token, $admin_token, $login);

$response["token"] = $token;

echo  json_encode($response);



