<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/telegram/api/utils.php";

$gas_domain = get_required(gas_domain);
$gas_address = get_required(gas_address);
$gas_password = get_required(gas_password);

if (!DEBUG) error("cannot use not in debug session");

tokenAccountReg($gas_domain, $gas_address, $gas_password);

echo json_encode(["success" => true]);


