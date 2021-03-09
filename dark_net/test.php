<?php


include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_net/mail.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_net/telegram.php";

//send("se", "sefsef", "x29a100@mail.ru");


$response = telegramGetUpdates("439686812");
$lastId = 0;
foreach ($response["result"] as $update) {
    $lastId = $update["update_id"];
    $chat_id = $update["message"]["chat"]["id"];
    telegramSend($chat_id, "messag222e");
}
telegramGetUpdates($lastId + 1);
echo json_encode($response);



