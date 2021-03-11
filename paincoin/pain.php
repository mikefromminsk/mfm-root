<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_net/mail.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_net/telegram.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/paincoin/utils.php";

$text = get_required("text");
$email = get_required("email");

description("input pain message");

$request_id = random_id();

dataCreate(["requests"], $admin_token);
$response["success"] = dataSet(["requests", $request_id], $admin_token, array(
    "request_id" => $request_id,
    "text" => $text,
    "email" => $email,
)) ? true : false;


/*telegramChatId();
telegramSend($text . "\napprove" . $approve_link . "\nreject" . $reject_link);
*/

//send mail with request id
$response["request"] = dataGet(["requests", $request_id], $admin_token);

echo json_encode($response);



