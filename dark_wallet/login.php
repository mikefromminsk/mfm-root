<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/domain_utils.php";

$login = get_required("login");
$password = get_required("password");

// test login

request("12312312312312", array(), array());

data_put("users.$login", "123");
$response = data_get("users.$login");

echo json_encode($response);