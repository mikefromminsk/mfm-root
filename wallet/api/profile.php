<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);
$address = get_string(address);

$coin = dataWalletProfile($domain, $address);
$coin[domain] = $domain;
$coin[title] = dataGet([wallet, info, $domain, title]);
$coin[hide_in_store] = dataGet([wallet, info, $domain, hide_in_store]) == 1;
$coin[total] = dataGet([wallet, info, $domain, total]);
$coin[owner] = dataGet([wallet, info, $domain, owner]);
$coin[trans] = dataCount([$domain, trans]);
$coin[wallets] = dataCount([$domain, wallet]);
$coin[nodes] = 1;
$coin[created] = dataInfo([$domain])[data_time];
$coin[dapps] = [];
$coin[ui] = dataGet([wallet, info, $domain, ui]);
$coin[mining] = dataExist([$domain, mining]);
$coin[description] = dataGet([wallet, info, $domain, description]);
$coin[ico_balance] = dataWalletBalance($domain, ico);
$coin[gas_balance] = dataWalletBalance($gas_domain, $address);


foreach (dataKeys([$domain, packages]) as $app_domain) {
    $coin[dapps][$app_domain] = [
        hash => dataGet([$domain, packages, $app_domain, hash]),
    ];
}

function addVolume(&$coin, $domain, $key)
{
    $coin[$key] = dataGet([analytics, $domain, $key, "S" . (60 * 60 * 24), value]);
    $coin[$key . "7d"] = 15500;
    $coin[$key . "7dChange"] = 21;
}

function addCandles(&$coin, $domain, $key)
{
    $val = dataGet([analytics, $domain, $key, "S" . (60 * 60 * 24), open]);
    if ($val == null) return;
    $val24h = 12;//dataGet([analytics, $domain, $key, "S" . (60 * 60 * 24), open]);
    $val7d = dataGet([analytics, $domain, $key, "S" . (60 * 60 * 24 * 7), open]);
    $coin[$key] = $val;
    $coin[$key . "24h"] = ($val24h - $val) / $val;
    $coin[$key . "7d"] = ($val7d - $val) / $val;
    $coin[$key . "7dChange"] = 12;
}

addVolume($coin, $domain, wallets);
addVolume($coin, $domain, transfers);
addVolume($coin, $domain, volume);
addCandles($coin, $domain, price);
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

commit($coin);