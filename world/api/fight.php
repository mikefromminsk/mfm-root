<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$attacker_address = get_required(gas_address);
$fight_pos = get_required(fight_pos);
$defender_path = get_required(defender_path);

$attacker_path = implode("/", [world, avatar, $attacker_address]);

$attacker = dataObject([$attacker_path], 100);
$defender = dataObject([$defender_path], 100);

$attacker_damage = 1;
$defender_damage = 1;

$attacker_health = $attacker[health] ?: 1;
$defender_health = $defender[health] ?: 1;

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

$loser = $attacker_health < 0 ? $attacker : $defender;
$loser_path = $attacker_health < 0 ? $attacker_path : $defender_path;
$winner_path = $attacker_health < 0 ? $defender_path : $attacker_path;

foreach ($loser[inventory] as $domain => $amount) {
    worldSend($domain, [$loser_path], [$winner_path], $amount);
}

if ($loser[address] != null) {
    teleport($loser[address], $loser[spawn][scene] ?: "home", $loser[scene][pos] ?: "0:0");
} else {
    dataSet([$loser_path], null);
}

commit();