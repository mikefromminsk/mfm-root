<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/auth.php";

$response["account"] = dataGet(["users", $login], $admin_token);

echo json_encode($response);