<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$path = get_required(path);

$path = explode("/", $path);

$response[value] = dataGet($path);

echo json_encode($response);