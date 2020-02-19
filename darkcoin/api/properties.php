<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/darknode/properties.php";

$hosting_minutes_for_one_usd = "";

$stock_url = "";

$email_server_host = "";
$email_server_security = "";
$email_server_port = "";
$email_login = "";
$email_password = "";

$yandex_money_wallet_id = "";
$yandex_money_secret_code = "";

$stock_fee_in_rub = "";

include_once $_SERVER["DOCUMENT_ROOT"] . "/darkcoin/api/properties_overload.php";

if (
    $hosting_minutes_for_one_usd == null
    || $stock_url == null
    || $email_server_host == null
    || $email_server_security == null
    || $email_server_port == null
    || $email_login == null
    || $email_password == null
    || $yandex_money_wallet_id == null
    || $yandex_money_secret_code == null
    || $stock_fee_in_rub == null
)
    die(json_encode(array("message" => "Please fill server paramters for darkcoin")));
