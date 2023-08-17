<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt/utils.php";

$deposit_address = get_required(deposit_address);

if (!dataExist([usdt, deposit, $deposit_address])) error("deposit address is not exist");

$receiver = dataGet([usdt, deposit, $deposit_address, receiver]);
$deadline = dataGet([usdt, deposit, $deposit_address, deadline]);
if ($deadline < time()) error("deposit time is finished");

$trans = //usdtTrc20Transactions($deposit_address);
    [
        [amount => 100]
    ];
$deposited = 0;
foreach ($trans as $tran) {
    if (true)/*($tran[time_ts] > $deadline - USDT_TRC20_DEPOSIT_INTERVAL)*/ {
        $deposited += $tran[amount];
        dataSend([usdt, wallet], USDT_OWNER, $receiver, $tran[amount]);
    }
}

$response[deposited] = $deposited;

echo json_encode($response);
