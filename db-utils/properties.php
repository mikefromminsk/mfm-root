<?php

$db_host = "localhost";
$db_name = "darkwallet";
$db_user = "root";
$db_pass = "root";

if (!isset($db_host) || !isset($db_user) || !isset($db_pass) || !isset($db_name))
    die(json_encode(array("message" => "Сreate properties.php with database connection parameters")));
