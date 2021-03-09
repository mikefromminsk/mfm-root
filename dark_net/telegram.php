<?php


function telegramSend($chatID, $message)
{
    file_get_contents("https://api.telegram.org/bot" . $GLOBALS["telegram_token"] . "/sendMessage?"
        . http_build_query(array(
            "text" => $message,
            "chat_id" => $chatID,
        )));
}


function telegramGetUpdates($offset = null)
{
    $response = file_get_contents("https://api.telegram.org/bot" . $GLOBALS["telegram_token"] . "/getUpdates?offset=$offset");
    return json_decode($response, true);
}
