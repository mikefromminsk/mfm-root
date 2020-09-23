<?php

include_once "token.php";
include_once "yandex_utils.php";

$yandex_wallet = get_required("yandex_wallet");
$amount = get_required("amount");

//balance test

$success = insertRow("actions", array(
    "action_amount" => $amount,
    "action_currency" => "RUB",
    "action_datetime" => time(),
    "action_complete" => "0",
    "user_sender" => 1,
    "user_receiver" => $user_id,
));
if (!$success)
    error("action not created");

$action_id = get_last_insert_id();

$request_payment_response = yandex("request-payment", array(
    "pattern_id" => "p2p",
    "to" => $yandex_wallet,
    "amount" => $amount,
    "comment" => "Вывод средств",
    "message" => "Вывод средств",
    "label" => $action_id,
));


if ($request_payment_response["status"] != "success")
    error("error request-payment");


$request_payment_response = yandex("process-payment", array(
    "request_id" => $request_payment_response["request_id"],
));

echo json_encode($request_payment_response);
