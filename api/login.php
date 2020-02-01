<?php

include_once "../db.php";
$node_url = uencode($node_url);

$user_login = get("user_login");
$user_password = get("user_password");
$token = get_int("token");
$stock_token = get("stock_token");
$message = "";
$user = null;

if ($user_login != null && $user_password != null) {
    $user = selectMap("select * from users where user_login = '$user_login'");
    $password_hash = hash("sha256", $user_password);
    if ($user != null) {
        $token = random_id();
        if ($user["user_password_hash"] == $password_hash) {
            updateList("users", array(
                "user_session_token" => $token,
            ), "user_id", $user["user_id"]);
        } else {
            $message = "Password is not correct";
        }
    } else {
        $token = random_id();
        insertList("users", array(
            "user_login" => $user_login,
            "user_password_hash" => $password_hash,
            "user_session_token" => $token,
            "user_stock_token" => random_id(),
        ));
    }
}

if ($stock_token != null) {
    $user = selectMap("select * from users where user_session_token = $stock_token");
    $token = $stock_token;
    if ($user == null)
        insertList("users", array(
            "user_login" => "user" . rand(1, 1000000),
            "user_password_hash" => hash("sha256", "pass" . rand(1, 1000000)),
            "user_session_token" => $stock_token,
            "user_stock_token" => random_id(),
        ));
}

if ($user == null && $token != null)
    $user = selectMap("select * from users where user_session_token = $token");

$user_id = $user["user_id"];

if ($message == null && ($user == null || $token == null || $user_id == null))
    $message = "login_error";

if ($message != null)
    die(json_encode(array(
        "message" => $message
    )));