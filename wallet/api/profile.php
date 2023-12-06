<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);

$coin[domain] = $domain;
$coin[total] = dataGet([wallet, info, $domain, total]);
$coin[owner] = dataGet([wallet, info, $domain, owner]);
$coin[logo] = "/wallet/img/coin.svg";
$coin[category] = dataGet([wallet, info, $domain, category]);

function addValue(&$coin, $domain, $key)
{
    $val = dataGet([analytics, $domain, $key, "S" . (60 * 60 * 24), open]);
    $val24h = 12;//dataGet([analytics, $domain, $key, "S" . (60 * 60 * 24), open]);
    $val7d = dataGet([analytics, $domain, $key, "S" . (60 * 60 * 24 * 7), open]);
    $coin[$key] = $val;
    $coin[$key . "24h"] = ($val24h - $val) / $val;
    $coin[$key . "7d"] = ($val7d - $val) / $val;
    $coin[$key . "7dChange"] = 12;
}

function addSum(&$coin, $domain, $key)
{
    $coin[$key] = dataGet([analytics, $domain, $key, "S" . (60 * 60 * 24), value]);
    $coin[$key . "7d"] = 15500;
    $coin[$key . "7dChange"] = 21;
}

addValue($coin, $domain, price);
addSum($coin, $domain, wallets);
addSum($coin, $domain, transfers);
addSum($coin, $domain, volume);
$coin[mcap] = $coin[total] * $coin[price];

$coin[pie][blocked] = 0;
$coin[pie][unused] = dataGet([$domain, wallet, $coin[owner], amount]);
if (dataGet([$domain, wallet, $coin[owner], script])) {
    $coin[pie][unused] = 0;
    $coin[pie][blocked] = $coin[pie][unused];
}
$coin[pie][circulation] = $coin[total] - $coin[pie][unused];
$coin[pie][ico] = dataGet([$domain, wallet, ico, amount]);
$coin[pie][bonus] = dataGet([$domain, wallet, bonus, amount]);

/*$coin[logo] = dataGet([wallet, info, $domain, logo]);

$coin[pie][deligated] = dataGet([$domain, wallet, $coin[owner], script]);

created
rating
coinlib.io
investors
plan whitepaper
lang
socnets

trending
topvolume
toptrades


*/

foreach (dataKeys([store, $domain]) as $hash)
    $coin[contracts][$hash] = dataGet([store, $domain, $hash]);

commit($coin);