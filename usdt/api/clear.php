<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt/api/utils.php";

$setted = 0;
foreach (USDT_TRC20_DEPOSIT_ADDRESSES as $DEPOSIT_ADDRESS) {
    dataSet([usdt, deposit, $DEPOSIT_ADDRESS, deadline], 0);
    $setted++;
}

$response[addresses] = USDT_TRC20_DEPOSIT_ADDRESSES;
$response[success] = $setted == sizeof(USDT_TRC20_DEPOSIT_ADDRESSES);

commit($response, usdt_deposit_clear);