<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/auth.php";

$domain_name = get_required("domain_name");
$output_count = get_required("output_count");

description("output");

$user_domain_count = dataGet(["users", $login, $domain_name], $admin_token);

$response["keys"] = null;
if ($user_domain_count >= $output_count) {
    $response["keys"] = dataGet(["store", $domain_name], $admin_token, null, 0, $output_count);
} else {
    error("you dont have enough domains");
}

echo json_encode($response);