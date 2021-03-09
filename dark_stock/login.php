<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/utils.php";

$login = get_required("login");
$password = get_required("password");

description("login on server");

$user_password_hash = hash_sha56($password);

$password_hash = dataGet(["users","$login"], "password_hash", $admin_token);

$response = array();
if ($user_password_hash == $password_hash) {
    $response["token"] = random_id();
    dataSet(["tokens", $response["token"]], $admin_token, $login);
}

echo json_encode($response);