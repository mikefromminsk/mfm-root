<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";

$login = get_required("login");
$domain_name = get_required("domain_name");
$count = get_required("count");
//$keys = get_required("keys");


/*$response["added"] = 0;
foreach ($keys as $name => $key) {
    $new_key = random_id();
    $valid = domain_set($host_name, $name, $key, $new_key, null);
    if ($valid) {
        dataSet(["store", $domain_name, $name], $admin_token, $key);
        $response["added"] += dataInc(["users", $login, $domain_name], $admin_token, 1) ? 1 : 0;
    }
}*/

$response["added"] = 0;
if (dataCreate(["users", $login], $admin_token, false)) {
    $response["added"] = dataInc(["users", $login, $domain_name], $admin_token, $count) ? $count : 0;
}

echo json_encode($response);