<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);

$coin[total] = dataGet([wallet, info, $domain, total]);
$coin[owner] = dataGet([wallet, info, $domain, owner]);
$coin[category] = dataGet([wallet, info, $domain, category]);

function addValue(&$coin, $domain, $key){
    $val = dataGet([analytics, $domain, $key, "S" . (60 * 60 * 24), open]);
    $val24h = dataGet([analytics, $domain, $key, "S" . (60 * 60 * 24), open]);
    $val7d = dataGet([analytics, $domain, $key, "S" . (60 * 60 * 24 * 7), open]);
    $coin[$key] = $val;
    $coin[$key . "24h"] = ($val24h - $val) / $val;
    $coin[$key . "7d"] = ($val7d - $val) / $val;
}

function addSum(&$coin, $domain, $key){
    $coin[$key] = dataGet([analytics, $domain, $key, "S" . (60 * 60 * 24), value]);
}

addValue($coin, $domain, price);
addSum($coin, $domain, wallets);
addSum($coin, $domain, transfer);
addSum($coin, $domain, volume);

$coin[mcap] = $coin[total] * $coin[price];


$coin[pie][unused] = $coin[total] - dataGet([$domain, wallet, $coin[owner], amount]);
$coin[pie][ico] = dataGet([$domain, wallet, ico, amount]);
$coin[pie][bonus] = dataGet([$domain, wallet, bonus, amount]);

/*$coin[logo] = dataGet([wallet, info, $domain, logo]);

$coin[pie][deligated] = dataGet([$domain, wallet, $coin[owner], script]);*/

foreach (dataKeys([store, $domain]) as $hash)
    $coin[contracts][$hash] = dataGet([store, $domain, $hash]);

commit($coin);