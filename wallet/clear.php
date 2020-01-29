<?php
include_once "../db.php";
query("delete from users");
query("delete from domains");
query("delete from domain_keys");
query("delete from offers");
query("delete from coins");

http_json_post($host_url . "create_coin", array(
    "user_login" => "ss",
    "user_password" => "123",
    "coin_name" => "ftr",
    "coin_code" => "FTR",
));

http_json_post($host_url . "create_coin", array(
    "user_login" => "ww",
    "user_password" => "123",
    "coin_name" => "usd",
    "coin_code" => "USD",
));

echo json_encode(true);
