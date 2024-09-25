<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/token/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

function teleport($address, $scene, $pos)
{
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


function teleportToSpawn($address)
{

}

function worldBalance($domain, array $address)
{
    return dataGet(array_merge($address, [inventory, $domain])) ?: 0;
}

function worldSend($domain, array $from_address, array $to_address, $amount)
{
    $from_balance = worldBalance($domain, $from_address);
    if ($from_balance < $amount) error("insufficient balance ($from_balance)");
    dataDec(array_merge($from_address, [inventory, $domain]), $amount);
    dataInc(array_merge($to_address, [inventory, $domain]), $amount);
}

function startsWith($haystack, $needle)
{
    return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
}

function endsWith($haystack, $needle)
{
    $pos = strrpos($haystack, $needle);
    if ($pos === false) {
        return null;
    }
    return substr($haystack, 0, $pos);
}

function randPos($scene, $x, $y, $distance)
{
    $enemy_pos = abs($x + rand(-$distance, $distance)) . ":" . abs($y + rand(-$distance, $distance));
    if ($scene[blocks][$enemy_pos] == null && $scene[enemies][$enemy_pos] == null) {
        return $enemy_pos;
    }
    return "$x:$y";
}

function countBlocks(&$scene, $base_x, $base_y, $distance, $domain)
{
    return countDomains($scene, blocks, $base_x, $base_y, $distance, $domain);
}

function countMobs(&$scene, $base_x, $base_y, $distance, $domain)
{
    return countDomains($scene, mobs, $base_x, $base_y, $distance, $domain);
}

function countAvatars(&$scene, $base_x, $base_y, $distance, $domain)
{
    return countDomains($scene, avatars, $base_x, $base_y, $distance, $domain);
}

function countDomains(&$scene, $path, $base_x, $base_y, $distance, $domain)
{
    $count = 0;
    for ($x = 0; $x < $scene[settings][width]; $x++) {
        for ($y = 0; $y < $scene[settings][height]; $y++) {
            if (abs($x - $base_x) <= $distance && abs($y - $base_y) <= $distance) {
                if ($scene[$path]["$x:$y"][domain] == $domain) {
                    $count++;
                }
            }
        }
    }
    return $count;
}
