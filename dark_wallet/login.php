<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/utils.php";

$login = get_required("login");
$password = get_required("password");

description("login on server");

$response["token"] = dataGet("users.$login", "token", $password);

echo json_encode($response);