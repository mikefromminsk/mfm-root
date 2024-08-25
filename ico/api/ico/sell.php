<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$amount = get_int_required(amount);
$key, $next_hash, $amount, $price


$domain = getDomain();
$gas_address = get_required(gas_address);
$owner_address = dataGet([wallet, info, $domain, owner]);

if ($gas_address == $owner_address) {
    if (!dataExist([$domain, token, ico])) {
        tokenScriptReg($domain, $domain . _ico, "$domain/api/token/ico/buy.php");
        tokenScriptReg(usdt, $domain . _ico, "$domain/api/token/ico/sell.php");
    }
    tokenSend($domain, $gas_address, ico, $amount, $key, $next_hash);
    dataSet([$domain, price], $price);
} else {
    $token_price = dataGet([$domain, price]);
    $total_usdt = $amount * $token_price;
    tokenSend($domain, $gas_address, $domain . _ico, $amount, $key, $next_hash);
    tokenSend(usdt, $domain . _ico, $gas_address, $total_usdt);
}