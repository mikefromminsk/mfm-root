<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/utils.php";

description("rates");

$response["rates"] = dataGet(["rates"], $admin_token);
$response["volume"] = dataGet(["volume"], $admin_token);

echo json_encode($response);