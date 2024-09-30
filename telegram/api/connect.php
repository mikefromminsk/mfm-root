<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/telegram/api/utils.php";

$address = get_required(address);
$username = get_required(username);

dataSet([accounts, $address], $username);

spendGasOf(get_required(gas_address), get_required(gas_password));
commit();