<?php
$db_host = ""; // localhost
$db_name = "";
$db_user = "";
$db_pass = "";
$api_url = ""; // http://localhost/
$start_node_locations = [];

$stock_api_url = ""; // http://localhost/store/

$email_server_host = ""; //mail.example.com
$email_server_security = ""; // tls || ssl || empty string
$email_server_port = ""; // tls = 587 || ssl = 465
$email_login = ""; //admin@example.com
$email_password = ""; //********

$yandex_money_wallet_id = "";
$yandex_money_secret_code = "";

$usd_rub_rate = null;
$stock_fee_in_usd = null;

include_once "properties_overload.php";

$stock_fee_in_rub = 5; //$stock_fee_in_usd * $usd_rub_rate;

if ($db_host == null || $db_name == null || $db_user == null || $db_pass == null
    || $api_url == null || $start_node_locations == null || sizeof($start_node_locations) == 0
    || $stock_api_url == null || $email_server_host == null || $email_server_port == null
    || $email_login == null || $email_password == null
    || $yandex_money_wallet_id == null || $yandex_money_secret_code == null
    || $stock_fee_in_rub == null || $stock_fee_in_usd == null)
    die(json_encode(array(
        "message" => "Please fill all server parameters in the properties.php file on this server"
    )));
