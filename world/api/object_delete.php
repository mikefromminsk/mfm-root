<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$gas_address = get_required(gas_address);
$scene = get_required(scene);
$x = get_int_required(x);
$y = get_int_required(y);

$texture = dataGet([world, $scene, objects, "$x:$y", texture]);
if ($texture == null) error("object does not exist");

tokenSend($texture, world, $gas_address, 1);

dataSet([world, $scene, objects, "$x:$y", texture], null);

$response[succes] = true;

commit($response);
