<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$gas_address = get_required(gas_address);
$scene = get_required(scene);
$x = get_int_required(x);
$y = get_int_required(y);

$pos = "$x:$y";

$object_domain = dataGet([world, $scene, objects, $pos, domain]);
if ($object_domain != null) {
    dataSet([world, $scene, objects, $pos, domain], null);
    dataInc([world, avatar, $gas_address, inventory, $object_domain]);
}

function move($gas_address, $scene, $pos) {
    $prev_scene = dataGet([world, avatar, $gas_address, scene]);
    $prev_pos = dataGet([world, avatar, $gas_address, pos]);
    dataSet([world, $prev_scene, avatars, $prev_pos], null);

    dataSet([world, $scene, avatars, $pos], $gas_address);
    dataSet([world, avatar, $gas_address, scene], $scene);
    dataSet([world, avatar, $gas_address, pos], $pos);
}

function moveToSpawn($address) {
    $spawn_scene = dataGet([world, avatar, $address, spawn, scene]) ?: "home";
    $spawn_pos = dataGet([world, avatar, $address, spawn, pos]) ?: "0:0";
    move($address, $spawn_scene, $spawn_pos);
    dataSet([world, avatar, $address, health], 1);
}

$attacker_address = $gas_address;
$defender_address = dataGet([world, $scene, avatars, $pos]);
if ($defender_address != null) {
    $attacker_hand = dataGet([world, avatar, $attacker_address, hand]);
    $defender_hand = dataGet([world, avatar, $defender_address, hand]);

    $attacker_damage = dataGet([world, items, $attacker_hand, damage]) ?: 1;
    $defender_damage = dataGet([world, items, $defender_hand, damage]) ?: 1;

    $attacker_health_max = dataGet([world, avatar, $attacker_address, health]) ?: 1;
    $defender_health_max = dataGet([world, avatar, $defender_address, health]) ?: 1;

    $attacker_health = $attacker_health_max;
    $defender_health = $defender_health_max;
    
    while (true) {
        $defender_health -= $attacker_damage;
        if ($defender_health <= 0) {
            break;
        }
        $attacker_health -= $defender_damage;
        if ($attacker_health <= 0) {
            break;
        }
    }
    
    if ($attacker_health > 0) {
        move($attacker_address, $scene, $pos);
        dataSet([world, avatar, $attacker_address, health], $attacker_health);
    } else {
        moveToSpawn($attacker_address);
    }

    if ($defender_health > 0) {
        dataSet([world, avatar, $defender_address, health], $defender_health);
    } else {
        moveToSpawn($defender_address);
    }

    $loser = $attacker_health > 0 ? $defender_address : $attacker_address;
    $winner = $attacker_health > 0 ? $attacker_address : $defender_address;

    foreach (dataKeys([world, avatar, $loser, inventory]) as $domain) {
        $amount = dataGet([world, avatar, $winner, inventory, $domain, amount]);
        dataDec([world, avatar, $loser, inventory, $domain], $amount);
        dataInc([world, avatar, $winner, inventory, $domain], $amount);
    }

} else {
    move($attacker_address, $scene, $pos);
}

commit();
