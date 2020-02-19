<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/db-utils/db.php";

$access = get_required("access");
$message = null;

$message = $access == "admin" ? null : "access error";

if ($message == null) {

    //query("delete from users");
    //query("alter table users AUTO_INCREMENT = 1");
    query("delete from domains");
    query("delete from offers");
    query("delete from messages");
    query("delete from servers");

    if ($server_url != $stock_url)
        $message = $message ?: http_json_post($stock_url . "darkcoin/api/clear.php", array(
            "access" => $access,
        ))["message"];

    $message = $message ?: http_json_post($server_url . "darkcoin/api/coin_create.php", array(
        "user_login" => "x29a100@mail.ru",
        "user_password" => "12345678",
        "without_verification" => true,
        "coin_code" => "USD",
    ))["message"];

    $message = $message ?: http_json_post($server_url . "darkcoin/api/coin_create.php", array(
        "user_login" => "selevich@mail.ru",
        "user_password" => "12345678",
        "without_verification" => true,
        "coin_code" => "SIL",
    ))["message"];

    $message = $message ?: http_json_post($server_url . "darkcoin/api/exchange.php", array(
        "user_login" => "selevich@mail.ru",
        "user_password" => "12345678",
        "have_coin_code" => "SIL",
        "have_coin_count" => "200",
        "want_coin_code" => "USD",
        "want_coin_count" => "50",
    ))["message"];

    $message = $message ?: http_json_post($server_url . "darkcoin/api/exchange.php", array(
        "user_login" => "selevich@mail.ru",
        "user_password" => "12345678",
        "have_coin_code" => "SIL",
        "have_coin_count" => "200",
        "want_coin_code" => "USD",
        "want_coin_count" => "50",
    ))["message"];

    $message = $message ?: http_json_post($server_url . "darkcoin/api/exchange.php", array(
        "user_login" => "x29a100@mail.ru",
        "user_password" => "12345678",
        "have_coin_code" => "USD",
        "have_coin_count" => "5",
        "want_coin_code" => "SIL",
        "want_coin_count" => "20",
    ))["message"];

    $message = $message ?: http_json_post($server_url . "darkcoin/api/send.php", array(
        "user_login" => "selevich@mail.ru",
        "user_password" => "12345678",
        "coin_code" => "SIL",
        "coin_count" => "100",
        "receiver_user_login" => "x29a100@mail.ru",
    ))["message"];

    $message = $message ?: http_json_post($server_url . "darkcoin/api/exchange.php", array(
        "user_login" => "x29a100@mail.ru",
        "user_password" => "12345678",
        "have_coin_code" => "USD",
        "have_coin_count" => "5",
        "want_coin_code" => "SIL",
        "want_coin_count" => "20",
    ))["message"];


}

echo json_encode(array(
    "message" => $message,
));
