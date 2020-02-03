<?php
include_once "../db.php";
$access = get_required("access");
$message = null;

$message = $access == "admin" ? null : "access error";

if ($message == null) {

    query("delete from users");
    query("delete from domains");
    query("delete from domain_keys");
    query("delete from offers");
    query("delete from coins");

    $message = $message == null && http_json_post($exchange_host_url . "clear", array(
        "access" => $access,
    )) ? null : "stock clear error";

    $message = $message == null && http_json_post($host_url . "create_coin", array(
        "user_login" => "x29a100@mail.ru",
        "user_password" => "12345678",
        "coin_name" => "UsDollar",
        "coin_code" => "USD",
    )) ? null : "create USD error";

    $message = $message == null && http_json_post($host_url . "create_coin", array(
        "user_login" => "selevich@mail.ru",
        "user_password" => "12345678",
        "coin_name" => "Silinium",
        "coin_code" => "SIL",
    )) ? null : "create Silinium error";

    $login = http_json_post($host_url . "login_check", array(
        "user_login" => "selevich@mail.ru",
        "user_password" => "12345678"
    ));
    $message = $message == null && http_json_post($host_url . "exchange", array(
        "token" => $login["user_session_token"],
        "have_coin_code" => "SIL",
        "have_coin_count" => "2000",
        "want_coin_code" => "USD",
        "want_coin_count" => "50",
    )) ? null : "exchange error";

    $login = http_json_post($host_url . "login_check", array(
        "user_login" => "x29a100@mail.ru",
        "user_password" => "12345678"
    ));
    $message = $message == null && http_json_post($host_url . "exchange", array(
        "token" => $login["user_session_token"],
        "have_coin_code" => "USD",
        "have_coin_count" => "1",
        "want_coin_code" => "SIL",
        "want_coin_count" => "45",
    )) ? null : "exchange error";

}

echo json_encode(array(
    "message" => $message,
));
