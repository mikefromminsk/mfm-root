<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_net/mail.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_net/telegram.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/paincoin/utils.php";

$text = get_required("text");
$email = get_required("email");

description("input pain message");

$request_id = random_id();

$response["success"] = dataAdd(["requests", $request_id], $admin_token, array(
    "text" => $text,
    "email" => $email,
)) ? true : false;


/*telegramChatId();
telegramSend($text . "\napprove" . $approve_link . "\nreject" . $reject_link);
*/

echo json_encode($response);



