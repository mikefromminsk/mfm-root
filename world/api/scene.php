<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$scene = get_required(scene);

if (!dataExist([world, $scene])) error("scene does not exist");

$response[name] = $scene;
$response[width] = dataGet([world, $scene, settings, width]);
$response[height] = dataGet([world, $scene, settings, height]);
$response[texture] = dataGet([world, $scene, settings, texture]);

$object_pos = dataKeys([world, $scene, objects], 1, 1000);
$response[objects] = [];
foreach ($object_pos as $position) {
    $object[domain] = dataGet([world, $scene, objects, $position, domain]);
    if ($object[domain] == null) continue;
    $coords = explode(":", $position);
    $object[x] = $coords[0];
    $object[y] = $coords[1];
    $response[objects][] = $object;
}

$avatar_pos = dataKeys([world, $scene, avatars], 1, 1000);
$response[avatars] = [];
foreach ($avatar_pos as $position) {
    $avatar[address] = dataGet([world, $scene, avatars, $position]);
    if ($avatar[address] == null) continue;
    $coords = explode(":", $position);
    $avatar[x] = $coords[0];
    $avatar[y] = $coords[1];
    $response[avatars][] = $avatar;
}

commit($response);