<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$attacker_address = get_required(gas_address);
$defender_address = get_required(defender_address);
$defender_next_hash = get_required(defender_next_hash);

$current_attacker_next_hash = tokenAddress($gas_domain, $attacker_address)[next_hash];
$current_defender_next_hash = tokenAddress($gas_domain, $defender_address)[next_hash];

$ids = [$current_attacker_next_hash, $current_defender_next_hash];
asort($ids);
$fight_id = implode(":", $ids);

if ($defender_next_hash != $current_defender_next_hash) {
    if (dataExist([world, fight, $fight_id])){
        $response[fight] = dataObject([world, fight, $fight_id], 100);
        commit($response);
    } else {
        error("You need update data of defender");
    }
}

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
    //moveAvatar($attacker_address, $scene, $pos);
    dataSet([world, avatar, $attacker_address, health], $attacker_health);
} else {
    teleportToSpawn($attacker_address);
}

if ($defender_health > 0) {
    dataSet([world, avatar, $defender_address, health], $defender_health);
} else {
    teleportToSpawn($defender_address);
}

$loser = $attacker_health > 0 ? $defender_address : $attacker_address;
$winner = $attacker_health > 0 ? $attacker_address : $defender_address;

foreach (dataKeys([world, avatar, $loser, inventory], 100) as $domain) {
    $amount = dataGet([world, avatar, $winner, inventory, $domain, amount]);
    dataDec([world, avatar, $loser, inventory, $domain], $amount);
    dataInc([world, avatar, $winner, inventory, $domain], $amount);
}


$fight = [
    id => $fight_id,
    attacker => $attacker_address,
    defender => $defender_address,
    attacker_next_hash => $current_attacker_next_hash,
    defender_next_hash => $current_defender_next_hash,
    winner => $winner,
    loser => $loser,
];

dataSet([world, fight, $fight_id], $fight);

$response[fight] = $fight;

commit($response);