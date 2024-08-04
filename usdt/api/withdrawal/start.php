<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt/api/utils.php";

$address = get_required(address);
$key = get_required(key);
$next_hash = get_required(next_hash);
$withdrawal_address = get_required(withdrawal_address);
$amount = get_int_required(amount);
$provider = get_required(provider);

if ($provider != "TRON") error("this chain is not available");
$provider = PROVIDERS[$provider];

if ($amount <= $provider[fee]) error("Amount is too small");
if (dataWalletBalance(usdt, $address) - $amount < 0) error("usdt not enough");

$withdrawal_id = dataWalletSend(usdt, $address, usdt_withdrawals, $amount, $key, $next_hash);

dataSet([usdt, withdrawal, $withdrawal_id], [
    withdrawal_address => $withdrawal_address,
    amount => $amount - $provider[fee],
    username => $address,
    provider => $provider[name],
]);

dataSet([usdt, withdrawal, history], $withdrawal_id);

$response[success] = true;

commit($response, usdt_withdrawal_start);
