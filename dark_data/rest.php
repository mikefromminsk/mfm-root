<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";


$method = $_SERVER['REQUEST_METHOD'];

$path = get_required("path");

if ($_SERVER["REQUEST_METHOD"] == "GET"){
    $response["data"] = data_get($path);

} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = get("data");
    $response["success"] = data_put($path, $data);
}

description("get data from inner db");

echo json_encode($response);


