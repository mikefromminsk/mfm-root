<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt/api/utils.php";

$address = get_required(address);
$key = get_required(key);
$nexthash = get_required(nexthash);
$withdrawal_address = get_required(withdrawal_address);
$amount = get_required(amount);
$chain = get_required(chain);
$withdrawal_id = get_int_required(withdrawal_id);

if ($chain != "TRON") error("this chain is not available");
$provider = PROVIDERS[$chain];

if (dataWalletBalance(usdt, $address) < $amount) error("usdt not enough");

dataWalletSend(usdt, $address, usdt_withdrawals, $amount, $key, $nexthash);

dataSet([usdt, withdrawal, $address, $withdrawal_id], [
    withdrawal_address => $withdrawal_address,
    amount => $amount,
    chain => $chain,
]);

dataSet([usdt, withdrawal, $address, chain], $withdrawal_id);
dataSet([usdt, withdrawal_chain], $address);

$response[success] = true;

commit($response, usdt_withdrawal_start);
