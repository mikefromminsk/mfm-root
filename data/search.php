<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$path = get_required(path);
$search_text = get_string(search_text, "");

$response[result] = dataSearch($path, $search_text) ?: [];

echo json_encode_readable($response);
