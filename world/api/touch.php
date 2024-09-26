<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$gas_address = get_required(gas_address);
$scene = get_required(scene);
$pos = get_required(pos);

$avatar = implode("/", [world, avatar, $gas_address]);
$block = implode("/", [world, $scene, blocks, $pos]);

if (dataExist([$block])) {
    foreach (dataObject([$block, inventory], 100) as $domain => $amount) {
        worldSend($domain, [$block], [$avatar], $amount);
    }
    dataSet([world, $scene, blocks, $pos], null);
}

teleport($gas_address, $scene, $pos);

commit();

