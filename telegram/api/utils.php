<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/token/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/telegram/api/properties.php";


function telegramSendToUsername($bot, $username, $text)
{
    $telegram_bot_api = get_required(telegram_bot_apis)[$bot];
    return http_post("https://api.telegram.org/bot$telegram_bot_api/sendMessage", [
        chat_id => dataGet([users, $username, $bot]),
        text => $text,
    ]);
}


function telegramSendToAddress($bot, $address, $text)
{
    $username = dataGet([accounts, $address]);
    if ($username) {
        return telegramSendToUsername($bot, $username, $text);
    }
}