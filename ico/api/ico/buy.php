<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$amount = get_int_required(amount);
$gas_address = get_required(gas_address);
$gas_key = get_required(gas_key);

$domain = getDomain();
$temp_next_hash = md5($gas_key);

$owner_address = dataGet([wallet, info, $domain, owner]);
if ($gas_address == $owner_address) {
    tokenSend(usdt, $gas_address, $domain . _ico, $amount, $gas_key, $temp_next_hash);
} else {
    $token_price = dataGet([$domain, price]);
    $total_usdt = $amount * $token_price;
    tokenSend(usdt, $gas_address, $domain . _ico, $total_usdt, $gas_key, $temp_next_hash);
    tokenSend($domain, ico, $gas_address, $amount);
}