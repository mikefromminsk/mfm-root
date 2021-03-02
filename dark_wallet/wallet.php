<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/login.php";

description("save tokens on server");

$response["wallet"] = dataGet("users.$login", "wallet", $token);

echo json_encode($response);