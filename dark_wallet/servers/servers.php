<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/properties.php";

$response["servers"] = $servers;

echo json_encode($response);