<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$gas_address = get_required(gas_address);
$domain = get_required(domain);
$scene = get_required(scene);
$pos = get_required(pos);

$amount = dataGet([world, $scene, blocks, $pos, inventory, $domain]);

dataDec([world, $scene, blocks, $pos, inventory, $domain], $amount);
dataInc([world, avatar, $gas_address, inventory, $domain], $amount);

commit();