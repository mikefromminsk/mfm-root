<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$gas_address = get_required(gas_address);
$scene = get_required(scene);
$pos = get_required(pos);

$avatar = implode("/", [world, avatar, $gas_address]);
$block = implode("/", [world, $scene, blocks, $pos]);

if (dataExist([$block])) {
    foreach (dataKeys([$block, inventory], 100) as $domain) {
        $amount = dataGet([$block, inventory, $domain]);
        //dataDec([world, $scene, blocks, $pos, inventory, $domain], $amount);
        dataInc([$avatar, inventory, $domain], $amount);
    }
    // $domain = dataGet([world, $scene, blocks, $pos, domain]);
    // send to admin avatar
    dataSet([world, $scene, blocks, $pos], null);
}

teleport($gas_address, $scene, $pos);

commit();

