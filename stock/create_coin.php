<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/auth.php";

$ticker = get_required_uppercase("ticker");
insertRow("coins", [ticker => $ticker]);
$response["result"] = incBalance($user_id, $ticker, 10000);
echo json_encode($response);