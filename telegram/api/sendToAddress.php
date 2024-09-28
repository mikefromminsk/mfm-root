<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/telegram/api/utils.php";

$bot = get_required(bot);
$address = get_required(address);
$text = get_required(text);
$password = get_required(password);
$gas_password = get_required(gas_password);

if ($password != $gas_password) error("password error");

telegramSendToAddress($bot, $address, $text);