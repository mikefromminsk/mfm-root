<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$gas_address = get_required(gas_address);
$scene = get_required(scene);
$domain = get_required(domain);
$pass = get_required(pass);
$x = get_int_required(x);
$y = get_int_required(y);


if (dataGet([world, $scene, objects, "$x:$y", texture]) != null) error("object already exists");

tokenScriptReg($domain, world, "world/api/object_delete.php");
tokenSend($domain, $gas_address, world, 1, $pass);

dataSet([world, $scene, objects, "$x:$y", texture], $domain);

$response[succes] = true;

commit($response);