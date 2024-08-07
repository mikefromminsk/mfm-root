<?php
include_once "auth.php";

$order_id = get_int(order_id);

$response["result"] = cancel($user_id, $order_id);

echo json_encode($response);