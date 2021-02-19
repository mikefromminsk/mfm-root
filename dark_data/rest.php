<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/utils.php";

$method = $_SERVER['REQUEST_METHOD'];

$path = get_required("path");
$hash = get("hash");
$data = get("data");
$level = get("level", 0);

if ($data == null) {
    $response["data"] = data_get($path, $level);
} else {
    $response["success"] = data_put($path, $data);
    $response["data"] = data_get($path, $level);
}

description("get data from inner db");

echo json_encode($response, JSON_FORCE_OBJECT);


