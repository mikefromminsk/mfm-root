<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/utils.php";

$path = get_path_required(path);
$search_text = get_required(search_text);

$response[result] = dataSearch($path, $search_text);

if ($response[result] == null) error("nothing found");

echo json_encode($response);