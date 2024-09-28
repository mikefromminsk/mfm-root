<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/telegram/api/utils.php";

$telegram_bot_api = get_required(telegram_bot_api);

$response[instraction] = [
    "// получи API ключ в BotFather",
    "// сгенерируй валидный сертификат",
    "// openssl req -x509 -days 365 -sha256 -nodes -newkey rsa:2048 -keyout webserver.key -out webserver.cert -subj \"/C=US/ST=New York/L=Brooklyn/O=Example Brooklyn Company/CN=mytoken.space\"",
    "// нужно отправить через postman. Запросом FormData",
    "тестим в https://api.telegram.org/bot$telegram_bot_api/getWebhookInfo"
];


$response[url] = "https://api.telegram.org/bot$telegram_bot_api/setWebhook";
$response[form_data] = [
    url => "https://mytoken.space/telegram/api/hook.php",
    certificate => "[c:\\webserver.cert]"
];
$response[success] = "you need to send it via postman";

echo json_encode($response);