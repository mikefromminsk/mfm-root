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

    $message = $message == null && http_json_post($host_url . "create_coin", array(
        "user_login" => "x29a100@mail.ru",
        "user_password" => "12345678",
        "coin_name" => "ftr",
        "coin_code" => "FTR",
    )) ? null : "create ftr error";

    $message = $message == null && http_json_post($host_url . "create_coin", array(
        "user_login" => "ww",
        "user_password" => "123",
        "coin_name" => "usd",
        "coin_code" => "USD",
    )) ? null : "create usd error";

}

echo json_encode(array("message" => $message));
