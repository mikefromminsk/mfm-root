<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$gas_address = get_required(gas_address);
$scene = get_required(scene);
$domain = get_required(domain);
$x = get_int_required(x);
$y = get_int_required(y);

$pos = "$x:$y";

if (dataGet([world, avatar, $gas_address, inventory, $domain]) <= 0)  error("Not enough items");

dataDec([world, avatar, $gas_address, inventory, $domain]);
dataSet([world, $scene, objects, $pos, domain], $domain);

commit();