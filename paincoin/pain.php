<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_net/mail.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_net/telegram.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/paincoin/utils.php";

$text = get_required("text");
$email = get_email_required("email");

description("input pain message");

$request_id = random_id();

dataCreate(["requests"], $admin_token);
$response["success"] = dataSet(["requests", $request_id], $admin_token, array(
    "request_id" => $request_id,
    "rate" => 0,
    "text" => $text,
    "email" => $email,
)) ? true : false;


$response["rated"] = dataAdd(["rate", 0], $admin_token, $request_id) ? true : false;


$approve_link = "<a href='http://$host_name/paincoin/approve.php?request_id=$request_id'>Approve</a>";
$reject_link = "<a href='http://$host_name/paincoin/reject.php?request_id=$request_id'>Reject</a>";
$message = $text . "\n\n" . $approve_link . "  " . $reject_link;

telegramSend($telegram_token, $telegram_chat_id, $message);

/*$response["mail_send"] = mailSend($email,
    "Ваша заявка принята",
    "Ваша заявка принята под номером $request_id");*/

$response["request"] = dataGet(["requests", $request_id], $admin_token);

echo json_encode($response);



