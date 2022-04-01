<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock0/utils.php";

$login = get_required("login");
$password = get_required("password");

description(basename(__FILE__));

$user_password_hash = hash_sha56($password);

$password_hash = dataGet(["users","$login", "password_hash"], $pass);

$response = array();
if ($user_password_hash == $password_hash) {
    $response["token"] = $login;//todo random_id();
    dataSet(["tokens", $response["token"]], $pass, $login);
}

echo json_encode($response);