<?php

include_once "token.php";

$action_amount = get_required("action_amount");

$success = insertRow("actions", array(
    "action_amount" => $action_amount,
    "action_currency"=> "RUB",
    "action_datetime" => time(),
    "action_complete" => "0",
    "user_sender" => $user_id,
    "user_receiver" => 1,
));
if (!$success)
    error("action not created");

$response["action_id"] = get_last_insert_id();

echo json_encode_readable($response);

