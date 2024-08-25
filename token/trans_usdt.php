<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$address = get_required(address);

$ids = dataHistory([usdt, withdrawal, $address, chain]);

$trans = [];
foreach ($ids as $withdrawal_id) {
    $tran = [];
    $tran[to] = dataGet([usdt, withdrawal, $address, $withdrawal_id, withdrawal_address]);
    $tran[success] = dataGet([usdt, withdrawal, $address, $withdrawal_id, success]);
    $tran[amount] = dataGet([usdt, withdrawal, $address, $withdrawal_id, amount]);
    $tran[time] = dataInfo([usdt, withdrawal, $address, $withdrawal_id])[data_time];
    $trans[] = $tran;
}

$response[trans] = $trans;

commit($response);