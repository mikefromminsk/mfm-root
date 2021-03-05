<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/utils.php";

$from = get_required("from");
$to = get_required("to");

$response["from"] = dataGet("request.$from", $to, $admin_token, "desc", 0, 10);
$response["to"] = dataGet("request.$to", $from, $admin_token, "desc", 0, 10);

echo json_encode($response);