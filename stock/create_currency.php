<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/auth.php";

$ticker = get_required_uppercase("ticker");
insertRow("currencies", [ticker => $ticker, rate => 0]);
$response["result"] = incBalance($user_id, $ticker, 10000);
echo json_encode($response);