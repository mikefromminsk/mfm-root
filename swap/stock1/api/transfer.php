<?php

include_once "auth.php";

$ticker = get_required_uppercase(ticker);
$amount = get_int_required(amount);
$to_user_id = get_int(to_user_id);
$to_email = get_string(to_email);

if ($to_email != null && $to_user_id == null)
    $to_user_id = scalarWhere(users, user_id, [email => $to_email]);

if ($to_user_id == null) error("user not found");

$response[result] = transfer(INTERNAL, $user_id, $to_user_id, $ticker, $amount) != null;

echo json_encode($response);