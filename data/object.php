<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$path = get_required(path);
$limit = get_int_required(limit, 100);

$path = explode("/", $path);

$response[object] = dataObject($path, $limit);

echo json_encode($response);
