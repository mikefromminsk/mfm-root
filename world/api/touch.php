<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$gas_address = get_required(gas_address);
$scene = get_required(scene);
$x = get_int_required(x);
$y = get_int_required(y);

$pos = "$x:$y";

if (dataExist([world, $scene, objects, $pos])) {
    dataSet([world, $scene, objects, $pos], null);
    $object_domain = dataGet([world, $scene, objects, $pos, domain]);
    dataInc([world, avatar, $gas_address, inventory, $object_domain]);
}

teleport($gas_address, $scene, $pos);

commit();

