<?php
include_once "../../db-utils/db.php";
$access = get_required("access");
$message = null;

$message = $access == "admin" ? null : "access error";

if ($message == null) {

    query("delete from users");
    query("alter table users AUTO_INCREMENT = 1");
    query("delete from domains");
    query("delete from offers");
    query("delete from messages");
    query("delete from servers");

    if ($server_url != $stock_url)
        $message = $message ?: http_json_post($stock_url . "darkcoin.store/api/clear.php", array(
            "access" => $access,
        ))["message"];

    $message = $message ?: http_json_post($server_url . "darkcoin.store/api/coin_create.php", array(
        "user_login" => "x29a100@mail.ru",
        "user_password" => "12345678",
        "without_verification" => true,
        "coin_code" => "USD",
    ))["message"];

    $message = $message ?: http_json_post($server_url . "darkcoin.store/api/coin_create.php", array(
        "user_login" => "selevich@mail.ru",
        "user_password" => "12345678",
        "without_verification" => true,
        "coin_code" => "SIL",
    ))["message"];

    $login = http_json_post($server_url . "darkcoin.store/api/login_check.php", array(
        "user_login" => "selevich@mail.ru",
        "user_password" => "12345678",
    ));
    $message = $message ?: $login["message"];

    $message = $message ?: http_json_post($server_url . "darkcoin.store/api/exchange.php", array(
        "token" => $login["user_session_token"],
        "have_coin_code" => "SIL",
        "have_coin_count" => "2000",
        "want_coin_code" => "USD",
        "want_coin_count" => "50",
    ))["message"];

    $login = http_json_post($server_url . "darkcoin.store/api/login_check.php", array(
        "user_login" => "x29a100@mail.ru",
        "user_password" => "12345678",
    ));
    $message = $message ?: $login["message"];

    $message = $message ?: http_json_post($server_url . "darkcoin.store/api/exchange.php", array(
        "token" => $login["user_session_token"],
        "have_coin_code" => "USD",
        "have_coin_count" => "1",
        "want_coin_code" => "SIL",
        "want_coin_count" => "45",
    ))["message"];

}

echo json_encode(array(
    "message" => $message,
));
