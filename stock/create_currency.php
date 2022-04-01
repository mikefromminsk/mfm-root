<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/auth.php";

$ticker = get_required("ticker");

insertRow("currencies", ["ticker" => $ticker, "rate" => 0]);
insertRow("balances", ["user_id" => $user_id, "ticker" => $ticker, "spot" => 100000, "blocked" => 0]);

$response["result"] = true;
echo json_encode($response);