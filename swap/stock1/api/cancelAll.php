<?php
include_once "auth.php";

$ticker = get_required_uppercase(ticker);

$response["result"] = cancelAll($user_id, $ticker);

echo json_encode($response);