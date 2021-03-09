<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/utils.php";

$give = get_required("give");
$want = get_required("want");

description("req");

$response["buy"] = dataGet(["requests", $give, $want], $admin_token, true, 0, 10);
$response["sale"] = dataGet(["requests", $want, $give], $admin_token, true, 0, 10);

echo json_encode($response);