<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/init.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/domain_utils.php";

data_put("users.user1", [123, array(123, 123,123), 444, "123123", [1, "123"], array("sdf" => 123)]);
$response = data_get("users.user1");

echo json_encode($response);