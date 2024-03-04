<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/utils.php";

$path = get_required(path);

$path = explode("/", $path);

$response[dir] = dataInfo($path);
$response[children] = [];

foreach (dataKeys($path) as $item) {
    $response[children][] = dataInfo(array_merge($path, [$item]));
}

echo json_encode($response);