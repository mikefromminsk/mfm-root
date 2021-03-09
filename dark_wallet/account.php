<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/auth.php";

description("save tokens on server");

$response["wallet"] = dataGet(["users", $login, "wallet"], $admin_token);
$response["income"] = dataGet(["income", $login], $admin_token);

echo json_encode($response);