<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/token/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$val = get_required(race_name);
$val = get_required(eat_category); // battery meat
$val = get_required(attack);

$response[content] = $val;

commit($response);