<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/utils.php";

$path = get_required(path);
$page = get_int(page, 1);
$size = get_int(size, 20);

$response[history] = dataHistory($path, $page, $size);

echo json_encode($response);