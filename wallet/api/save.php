<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/auth.php";

$domain_name = get_required_uppercase("domain_name");
$keys = get_required("keys");

description("save tokens on server");

$response["added"] = 0;
foreach ($keys as $key => $value)
    $response["added"] += dataSet(["users", $login, "wallet", $domain_name, $key], $token, $value) ? 1 : 0;

echo json_encode($response);