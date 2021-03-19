<?php

function telegramSend($telegram_token, $chatID, $message)
{
    return true;
    return json_decode(file_get_contents("https://api.telegram.org/bot$telegram_token/sendMessage?"
        . http_build_query(array(
            "text" => $message,
            "chat_id" => $chatID,
            "parse_mode" => "HTML",
        ))), true);
}

function telegramGetUpdates($telegram_token, $offset = null)
{
    return true;
    return json_decode(file_get_contents("https://api.telegram.org/bot$telegram_token/getUpdates?offset=$offset"), true);
}
