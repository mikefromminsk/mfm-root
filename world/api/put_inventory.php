<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$gas_address = get_required(gas_address);
$scene = get_required(scene);
$domain = get_required(domain);
$amount = get_required(amount);
$pos = get_required(pos);

if (dataGet([world, avatar, $gas_address, inventory, $domain]) < $amount)  error("Not enough items");

dataDec([world, avatar, $gas_address, inventory, $domain], $amount);
dataSet([world, $scene, blocks, $pos, inventory, $domain], $amount);

commit();
