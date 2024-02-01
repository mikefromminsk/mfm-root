<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt/api/utils.php";

$address = get_required(address);
$key = get_required(key);
$nexthash = get_required(nexthash);
$withdrawal_address = get_required(withdrawal_address);
$amount = get_required(amount);
$chain = get_required(chain);
$withdrawal_id = get_int_required(withdrawal_id);
$txid = get_required(txid);

if ($chain != "TRON") error("this chain is not available");
$provider = PROVIDERS[$chain];

dataWalletSend(usdt, usdt_withdrawals, $address, $amount, $key, $nexthash);

dataSet([usdt, withdrawal, $address, $withdrawal_id], [
    success => "1",
    txid => $txid,
]);

$response[success] = true;

commit($response, usdt_withdrawal_success);
