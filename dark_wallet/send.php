<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/login.php";

$receiver = get_required("receiver");
$domain_name = get_required_uppercase("domain_name");
$keys = get_required("keys");

description("send tokens to user");

dataId(["income"], $admin_token, true);

$response["added"] = 0;
foreach ($keys as $key => $value)
    $response["added"] += dataPut("income.$receiver.$login.$domain_name", $key, $admin_token, $value) ? 1 : 0;

echo json_encode($response);