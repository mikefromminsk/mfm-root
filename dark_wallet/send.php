<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/auth.php";

$receiver = get_required("receiver");
$domain_name = get_required_uppercase("domain_name");
$keys = get_required("keys");

description("send tokens to user");

dataCreate("income", $admin_token);

$response["added"] = 0;
foreach ($keys as $key => $value)
    $response["added"] += dataSet("income.$receiver.$login.$domain_name", $key, $admin_token, $value) ? 1 : 0;

echo json_encode($response);