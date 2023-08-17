<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$path = get_required(path);

$path_array = explode("/", $path);

$response = dataMeta($path_array);
if ($response == null) error("path '$path' not exist");

$response[path] = dataPath($response[data_id]);
$response[children] = dataChildren($path_array);

echo json_encode($response);