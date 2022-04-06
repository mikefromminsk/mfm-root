<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/api/auth.php";

$ticker = get_required_uppercase("ticker");
$name = get_required("name");

insertRow("coins", [ticker => $ticker, name => $name]);
$response["result"] = incBalance($user_id, $ticker, 10000);

echo json_encode($response);