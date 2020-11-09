<?php

//https://api.telegram.org/bot1429457912:AAGgqcB3QcaBlixFicG3mEM-eXtLyRLan6Y/setWebhook?url=https://darkcoin.store/wallet/bot.php

spl_autoload_register(function ($class_name) {
    include_once $_SERVER["DOCUMENT_ROOT"] . "/" . str_replace("\\", "/", $class_name) . ".php";
});

$token = "1429457912:AAGgqcB3QcaBlixFicG3mEM-eXtLyRLan6Y";
$bot = new \TelegramBot\Api\Client($token);

function showMenu($bot, $message, $text)
{
    $bot->sendMessage($message->getChat()->getId(), $text . "\n\nМеню:", null, false, null, new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup([
        [
            ["callback_data" => "credit", "text" => "Получить кредит"],
        ],
        [
            ["callback_data" => "balance", "text" => "Проверить баланс"],
        ],
        [
            ["url" => "https://darkcoin.store/cash?telegram_token=" . $message->getFrom()->getId(), "text" => "Получить наличные"],
        ],
        [
            ["url" => "https://darkcoin.store/shop?telegram_token=" . $message->getFrom()->getId(), "text" => "Открыть магазин"],
        ],
    ]));
}

$bot->command("start", function ($message) use ($bot) {
    showMenu($bot, $message, "Добрый день!");
});


$bot->callbackQuery(function ($callbackQuery) use ($bot) {
    $chatId = $callbackQuery->getMessage()->getChat()->getId();
    $bot->answerCallbackQuery($callbackQuery->getId());
    $data = $callbackQuery->getData();
    if (strpos($data, "add") === 0 && strlen($data) > 3 && is_numeric(substr($data, 3))) {
        $credit_sum = substr($data, 3);
        showMenu($bot, $callbackQuery->getMessage(), "Вы получили $credit_sum рублей на счет.");
    }
    if ($data == "credit")
        $bot->sendMessage($chatId, "Вам одобрен кредит до 300 рублей.", null, false, null, new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup([
            [
                ["callback_data" => "add100", "text" => "100 р."],
                ["callback_data" => "add150", "text" => "150 р."],
                ["callback_data" => "add200", "text" => "200 р."],
                ["callback_data" => "add250", "text" => "250 р."],
                ["callback_data" => "add300", "text" => "300 р."],
            ],
        ]));
    if ($data == "cash") {
        $bot->sendMessage($chatId, "Сколько денег тебе нужно?");
    }
    if ($data == "balance") {
        showMenu($bot, $callbackQuery->getMessage(), "У тебя 100 рублей");
    }
});


$bot->run();


