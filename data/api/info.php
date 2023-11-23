<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/utils.php";

$path = get_required(path);

$path_array = explode("/", $path);

$response = dataKeys($path_array);
if ($response == null) error("path '$path' not exist");

$response[children] = dataKeys($path_array);

echo json_encode($response);