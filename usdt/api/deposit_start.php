<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt/api/utils.php";

$address = get_required(address);
$chain = get_required(chain);

$provider = PROVIDERS[$chain];

if (dataExist([usdt, wallet, $address]) == null) error("address not exist");

foreach ($provider[deposit_addresses] as $token_deposit_address) {
    if (dataGet([usdt, deposit, $chain, $token_deposit_address, address]) == $address
        && intval(dataGet([usdt, deposit, $chain, $token_deposit_address, deadline])) > time()) {
        $deposit_address = $token_deposit_address;
        break;
    }
}

if ($deposit_address == null) {
    foreach ($provider[deposit_addresses] as $token_deposit_address) {
        if (dataGet([usdt, deposit, $chain, $token_deposit_address, deadline]) > time()) continue;
        $deposit_address = $token_deposit_address;
        break;
    }

    if ($deposit_address == null) error("all addresses are busy");

    dataSet([usdt, deposit, $chain, $deposit_address, address], $address);
    dataSet([usdt, deposit, $chain, $deposit_address, deadline], time() + USDT_TRC20_DEPOSIT_INTERVAL);
}

$response[deadline] =  dataGet([usdt, deposit, $chain, $deposit_address, deadline]);
$response[deposit_address] = $deposit_address;
$response[success] = true;

commit($response, usdt_deposit);