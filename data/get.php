<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$path = get_required(path);
$path = explode("/", $path);

echo json_encode(dataGet($path));