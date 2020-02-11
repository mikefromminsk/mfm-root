<?php
$db_host = ""; // localhost
$db_name = "";
$db_user = "";
$db_pass = "";
$host_url = ""; // http://localhost/
$start_node_locations = [];

$exchange_host_url = ""; // http://localhost/store/

$email_server_host = ""; //mail.example.com
$email_server_security = ""; // tls || ssl || empty string
$email_server_port = ""; // tls = 587 || ssl = 465
$email_login = ""; //admin@example.com
$email_password = ""; //********

include_once "properties_overload.php";

if ($db_host == null || $db_name == null || $db_user == null || $db_pass == null
    || $host_url == null || $start_node_locations == null || sizeof($start_node_locations) == 0
    || $exchange_host_url == null || $email_server_host == null || $email_server_port == null
    || $email_login == null || $email_password == null)
    die(json_encode(array(
        "message" => "Please fill all server parameters in the properties.php file on this server"
    )));
