<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";

$login = get_required("login");
$domain_name = get_required("domain_name");
$keys = get_required("keys");

$response["added"] = 0;
foreach ($keys as $name => $key) {
    $new_key = random_id();
    $valid = domain_set($host_name, $name, $key, $new_key, null);
    if ($valid) {
        dataSet("users.admin.wallet.$domain_name", $name, $admin_token, $key);
        $count = dataGet("users.$login.wallet", $domain_name, $admin_token);
        $response["added"] += dataSet("users.$login.wallet", $domain_name, $admin_token, $count + 1) ? 1 : 0;
    }
}

echo json_encode($response);