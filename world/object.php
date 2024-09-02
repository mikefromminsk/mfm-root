<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/token/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$val = get_required(width);
$val = get_required(height);
$val = get_required(texture);
$val = get_required(type); // portal tree

$response[content] = $val;

commit($response);