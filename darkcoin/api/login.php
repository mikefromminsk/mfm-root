<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db-utils/db.php";
include_once "messages_utils.php";

$node_url = uencode($server_url . "node");

$user_login = get("user_login");
$user_password = get("user_password");
$token = get_int("token");
$stock_token = get("stock_token");
$without_verification = get("without_verification");
$message = "";
$user = null;

if ($user_login != null && !filter_var($user_login, FILTER_VALIDATE_EMAIL))
    error("login is not email");

if ($user_login != null && $user_password != null) {
    $user = selectMap("select * from users where user_login = '$user_login'");
    $password_hash = hash("sha256", $user_password);
    if ($user != null) {
        if ($user["user_stock_token"] != null) {
            $token = random_id();
            if ($user["user_password_hash"] == $password_hash) {
                updateList("users", array(
                    "user_session_token" => $token,
                ), "user_id", $user["user_id"]);
                $user["user_session_token"] = $token;
            } else
                $message = "Password is not correct";
        } else
            $message = "Open your email and verify your account";
    } else {
        if ($without_verification != null) {
            $token = random_id();
            insertList("users", array(
                "user_login" => $user_login,
                "user_password_hash" => $password_hash,
                "user_session_token" => $token,
                "user_stock_token" => random_id(),
            ));
        } else {
            $token = random_id();
            $validation_link = str_replace("/api/", "", $server_url) . "#!/darkcoin/" . $token;
            $send_result = send($user_login, "Registration", "Click link follow: <a href='$validation_link'>$validation_link</a>");
            if ($send_result === true) {
                insertList("users", array(
                    "user_login" => $user_login,
                    "user_password_hash" => $password_hash,
                    "user_session_token" => $token,
                ));
                $message = "Please verify your email address";
            } else {
                $message = $send_result;
            }
        }
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

/*if ($stock_token != null && $user_password != null)
    //change passwrod*/

if ($user == null && $token != null)
    $user = selectMap("select * from users where user_session_token = $token");

$user_id = $user["user_id"];

if ($message == null && ($user == null || $token == null || $user_id == null))
    $message = "Login error";

if ($message != null)
    die(json_encode(array(
        "message" => $message
    )));

if ($user["user_stock_token"] == null) {
    $user["user_stock_token"] = random_id();
    updateList("users", array("user_stock_token" => $user["user_stock_token"]), "user_id", $user_id);
}




