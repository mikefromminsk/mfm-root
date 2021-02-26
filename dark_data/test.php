<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/init.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/utils.php";

$data_id = data_put("login.test", "pass", "123");

$root = data_get("login.test", "pass");

echo json_encode($root);