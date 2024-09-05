<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$scene = get_required(scene);

if (!dataExist([world, $scene])) error("scene does not exist");

$response[name] = $scene;
$response[width] = dataGet([world, $scene, settings, width]);
$response[height] = dataGet([world, $scene, settings, height]);
$response[texture] = dataGet([world, $scene, settings, texture]);
$positions = dataKeys([world, $scene, objects], 1, 1000);

$response[objects] = [];
foreach ($positions as $position) {
    $object[texture] = dataGet([world, $scene, objects, $position, texture]);
    if ($object[texture] == null) continue;
    $coords = explode(":", $position);
    $object[x] = $coords[0];
    $object[y] = $coords[1];
    $response[objects][] = $object;
}

$response[succes] = true;

commit($response);