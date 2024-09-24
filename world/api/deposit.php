<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$gas_address = get_required(gas_address);
$domain = get_required(domain);
$amount = get_int_required(amount);
$pass = get_required(pass);

if (tokenAddress($domain, world) == null)
    tokenScriptReg($domain, world, "world/api/move.php");

if ($domain == $gas_domain) error("cannot deposit gas");

tokenSend($domain, $gas_address, world, $amount, $pass);
dataInc([world, avatar, $gas_address, inventory, $domain], $amount);

commit();
