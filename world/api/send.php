<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$gas_address = get_required(gas_address);
$to_address = get_required(to_address);
$domain = get_required(domain);
$amount = get_int_required(amount);

worldSend($domain, $gas_address, $to_address, $amount);

commit();