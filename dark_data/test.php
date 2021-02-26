<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/utils.php";

$data_id = data_put("users.login.test", "pass", "123");
$data_id = data_put("users.login2.test", "pass", "123");

$root = data_get("users", 1);

echo json_encode($root);