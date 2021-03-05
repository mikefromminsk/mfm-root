<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/utils.php";

$from = get_required("from");
$to = get_required("to");

/*$response["from"] = dataGet("requests.$from", $to, $admin_token, "desc", 0, 10);
$response["to"] = dataGet("requests.$to", $from, $admin_token, "desc", 0, 10);*/
$response["test"] = dataGet("requests.$from", $to, $admin_token);

echo json_encode($response);