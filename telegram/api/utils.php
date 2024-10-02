<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/token/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/telegram/api/properties.php";

$telegram_bot_apis = get_required(telegram_bot_apis);
$bot = get_required(bot);

$telegram_bot_api = $telegram_bot_apis[$bot];


function telegramSend($bot, $username, $text)
{
    $telegram_bot_api = get_required(telegram_bot_api);
    return http_post("https://api.telegram.org/bot$telegram_bot_api/sendMessage", [
        chat_id => dataGet([telegram, users, $bot, $username]),
        text => $text,
    ]);
}


function telegramConnectAccountAndUser($address, $username, $domain)
{

}
