<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$scene = get_required(scene);
$address = get_required(address);
$x = get_int_required(x);
$y = get_int_required(y);
$speedX = get_int_required(speedX);
$speedY = get_int_required(speedY);

broadcast('move', [
    scene => $scene,
    address => $address,
    x => $x,
    y => $y,
    speedX => $speedX,
    speedY => $speedY,
]);

commit();