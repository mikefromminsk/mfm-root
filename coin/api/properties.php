<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/node/properties.php";

$hosting_minutes_for_one_usd = "";

$stock_url = "";

$email_server_host = "";
$email_server_security = "";
$email_server_port = "";
$email_login = "";
$email_password = "";

include_once $_SERVER["DOCUMENT_ROOT"] . "/darkcoin/api/properties_overload.php";

if (
    $hosting_minutes_for_one_usd == null
    || $stock_url == null
    || $email_server_host == null
    || $email_server_security == null
    || $email_server_port == null
    || $email_login == null
    || $email_password == null
)
    die(json_encode(array("message" => "Please fill server paramters for darkcoin")));
