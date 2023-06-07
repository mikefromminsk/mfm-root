<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt/utils.php";

$receiver = get_required(receiver);

$deposit_address = null;

foreach (USDT_TRC20_DEPOSIT_ADDRESSES as $DEPOSIT_ADDRESS) {
    if (dataGet([uset, deposit, $DEPOSIT_ADDRESS, deadline]) > time()) continue;
    $deposit_address = $DEPOSIT_ADDRESS;
    break;
}

if ($deposit_address == null) error("all addresses is busy");

dataSet([uset, deposit, $deposit_address, deadline], time() + USDT_TRC20_DEPOSIT_INTERVAL);

$response[result] = true;

echo json_encode($response);
