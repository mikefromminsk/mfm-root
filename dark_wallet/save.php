<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/auth.php";

$domain_name = get_required_uppercase("domain_name");
$keys = get_required("keys");

description("save tokens on server");

$response["added"] = 0;

$GLOBALS["test"] = 1;

foreach ($keys as $key => $value) {
    $response["added"] += dataPut("users.$login.wallet.$domain_name", $key, $token, $value) != null ? 1 : 0;
}

echo json_encode($response);