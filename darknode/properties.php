<?php

include_once $_SERVER["DOCUMENT_ROOT"] ."/db-utils/properties.php";

$server_url = "http://localhost/";

if (!isset($server_url))
    die(json_encode(array("message" => "Set \$server_url variable")));