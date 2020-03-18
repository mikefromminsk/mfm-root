<?php

$db_name = "";
$db_user = "";
$db_pass = "";

include_once "properties_overload.php";

if (
    $db_name == null
    || $db_user == null
    || $db_pass == null
)
    die(json_encode(array("message" => "Create properties.php with database connection parameters")));
