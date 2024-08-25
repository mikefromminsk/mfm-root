<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt_bridge/api/utils.php";

$address = get_required(address);
$chain = get_required(chain);

$provider = PROVIDERS[$chain];

if (dataExist([usdt, wallet, $address]) == null) error("address not exist");

// find deposit address
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
    dataSet([usdt, deposit, $chain, $deposit_address, deadline], time() + $provider[deadline_interval]);
}

$response[deadline] =  dataGet([usdt, deposit, $chain, $deposit_address, deadline]);
$response[deposit_address] = $deposit_address;
$response[min_deposit] = $provider[min_deposit];
$response[success] = true;

commit($response, usdt_deposit_start);