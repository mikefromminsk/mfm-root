<?php
$db_host = "";
$db_name = "";
$db_user = "";
$db_pass = "";
$host_url = "";
$start_node_locations = [];

$exchange_host_url = "";
$gmail_email = "";
$gmail_password = "";

include_once "properties_overload.php";

if ($db_host == null || $db_name == null || $db_user == null || $db_pass == null
    || $host_url == null || $start_node_locations == null || sizeof($start_node_locations) == 0
    || $exchange_host_url == null || $gmail_email == null || $gmail_password == null)
    die(json_encode(array(
        "message" => "Please fill all server parameters in the properties.php file on this server"
    )));
