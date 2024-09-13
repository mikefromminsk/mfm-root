<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/token/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

function teleport($address, $scene, $pos) {
    $prev_scene = dataGet([world, avatar, $address, scene]);
    $prev_pos = dataGet([world, avatar, $address, pos]);
    dataSet([world, $prev_scene, avatars, $prev_pos], null);

    dataSet([world, $scene, avatars, $pos], $address);
    dataSet([world, avatar, $address, scene], $scene);
    dataSet([world, avatar, $address, pos], $pos);

    $pos = explode(":", $pos);
    broadcast('teleport', [
        address => $address,
        scene => $scene,
        x => $pos[0],
        y => $pos[1],
    ]);
}


function teleportToSpawn($address) {
    $spawn_scene = dataGet([world, avatar, $address, spawn, scene]) ?: dataGet([world, avatar, $address, scene]);
    $spawn_pos = dataGet([world, avatar, $address, spawn, pos]) ?: "0:0";
    teleport($address, $spawn_scene, $spawn_pos);
    dataSet([world, avatar, $address, health], 1);
}
