<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$gas_address = get_required(gas_address);
$scene = get_required(scene);
$domain = get_required(domain);
$pos = get_required(pos);

if (dataGet([avatar, $gas_address, inventory, $domain]) <= 0)  error("Not enough items");

dataDec([avatar, $gas_address, inventory, $domain]);
dataSet([$scene, blocks, $pos, domain], $domain);

commit();