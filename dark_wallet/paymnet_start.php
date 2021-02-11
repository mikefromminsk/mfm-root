<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/utils.php";

$payment_host = get_required("payment_host");
$payment_currency = get_required("payment_currency");
$payment_amount = get_required("payment_amount");
$domain_name = get_required("domain_name");
$domain_postfix_length = get_required("domain_postfix_length");


$payment_id = insertRowAndGetId("payments", array(
    "payment_host" => $payment_host,
    "payment_currency" => $payment_currency,
    "payment_amount" => $payment_amount,
    "domain_name" => $domain_name,
    "domain_postfix_length" => $domain_postfix_length,
    "payment_create_time" => time(),
));

data_add("users.$login.private.payments", $token, $payment_id);

if ($payment_host == "yandex.ru" && $payment_currency == "RUB") {
    include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/yandex_start.php";
}
