<?php

$db_name = "";
$db_user = "";
$db_pass = "";
$host_name = "";

include_once $_SERVER["DOCUMENT_ROOT"] . "/db-utils/properties_overload.php";

if (
    $db_name == null
    || $db_user == null
    || $db_pass == null
    || $host_name == null
)
    die(json_encode(array("message" => "Create properties.php with database connection parameters")));
