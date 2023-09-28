<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt/utils.php";

$deposit_address = get_required(deposit_address);

if (!dataExist([usdt, deposit, $deposit_address])) error("deposit address is not exist");

$receiver = dataGet([usdt, deposit, $deposit_address, receiver]);
$last_tx_time = dataGet([usdt, deposit, $deposit_address, last_tx_time]) ?: 0;
//if ($deadline < time()) error("deposit time is finished");

$trans = [];//usdtTrc20Transactions($deposit_address);

$deposited = 5;
foreach ($trans as $tran) {
    if ($tran[time] > $last_tx_time) {
        $deposited += $tran[amount];
        $last_tx_time = $tran[time];
    }
}

if ($deposited > 0) {
    dataSet([usdt, deposit, $deposit_address, last_tx_time], $last_tx_time);
    dataWalletSend([usdt, wallet], USDT_OWNER, $receiver, $deposited);
}

$response[last_tx_time] = $last_tx_time;
$response[deposited] = $deposited;
$response[trans] = $trans;

commit($response, usdt_check);