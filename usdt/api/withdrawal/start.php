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

if ($amount < $provider[min]) error("minimum amount is $provider[min]");
if (dataWalletBalance(usdt, $address) < $amount + $provider[fee]) error("usdt not enough");

dataWalletRegScript(usdt,usdt_withdrawals, "usdt/api/withdrawal/success.php");
dataWalletRegScript($gas_domain,usdt_withdrawal_success, "usdt/api/withdrawal/success.php");

$withdrawal_id = dataWalletSend(usdt, $address, usdt_withdrawals, $amount + $provider[fee], $key, $next_hash);

dataSet([usdt, withdrawal, $withdrawal_id], [
    withdrawal_address => $withdrawal_address,
    amount => $amount,
    username => $address,
    provider => $provider[name],
]);

dataSet([usdt, withdrawal, history], $withdrawal_id);

$response[success] = true;

commit($response);
