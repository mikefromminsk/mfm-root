<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$path = get_required(path);

$path = explode("/", $path);

if (!dataExist($path)) error("Path not found");

$response[info] = dataInfo($path);

echo json_encode($response);