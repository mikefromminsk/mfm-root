<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/token/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$val = get_required("text/ww");

$response[content] = $val;

commit($response);