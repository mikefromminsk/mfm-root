<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/utils.php";

description("top");

$response["top"] = [];

$most_rated = dataGet(["rate"], $admin_token, false, 0, 10);
$most_rated = array_reverse($most_rated);
foreach ($most_rated as $rate => $rate_array)
    foreach ($rate_array as $request_id)
        $response["top"][] = dataGet(["requests", $request_id], $admin_token);

$response["last"] = array_values(dataGet(["requests"], $admin_token, null, 0, 10));

echo json_encode($response);
