<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$gas_address = get_required(gas_address);
$scene = get_required(scene);
$x = get_required(x);
$y = get_required(y);

dataSet([world, avatar, spawn, scene], $scene);
dataSet([world, avatar, spawn, pos], "$x:$y");

commit();
