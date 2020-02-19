<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db-utils/properties.php";

$server_url = "";

include_once $_SERVER["DOCUMENT_ROOT"] . "/darknode/properties_overload.php";

if (
    $server_url == null
)
    die(json_encode(array("message" => "Set \$server_url variable")));