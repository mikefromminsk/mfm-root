<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/track.php";

$domain = get_required(domain);
$address = get_string(address);

$coin = dataWalletProfile($domain, $address);
$coin[domain] = $domain;
$coin[title] = dataGet([wallet, info, $domain, title]);
$coin[hide_in_store] = dataGet([wallet, info, $domain, hide_in_store]) == 1;
$coin[total] = dataGet([wallet, info, $domain, total]);
$coin[owner] = dataGet([wallet, info, $domain, owner]);
$coin[trans] = dataCount([$domain, trans]);
$coin[wallets] = dataCount([$domain, token]);
$coin[nodes] = 1;
$coin[created] = dataInfo([$domain])[data_time];
$coin[dapps] = [];
$coin[ui] = dataGet([wallet, info, $domain, ui]);
$coin[mining] = dataExist([$domain, mining]);
$coin[description] = dataGet([wallet, info, $domain, description]);
$coin[ico_balance] = tokenAddressBalance($domain, ico);
$coin[gas_balance] = tokenAddressBalance($gas_domain, $address);


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

addVolume($coin, $domain, wallets);
addVolume($coin, $domain, transfers);
addVolume($coin, $domain, volume);


$coin[price] = dataGet([$domain, price]);
$coin[icoPrice] = getCandleLastValue($domain, price);
$coin[price24] = getCandleChange24($domain, price);
$coin[volume] = 100;
$coin[wallets] = 100;
$coin[transfers] = 100;
$coin[volume] = 100;
$coin[mcap] = $coin[total] * $coin[price];

$coin[pie][blocked] = 0;
$coin[pie][unused] = dataGet([$domain, token, $coin[owner], amount]);
if (dataGet([$domain, token, $coin[owner], script])) {
    $coin[pie][unused] = 0;
    $coin[pie][blocked] = $coin[pie][unused];
}
$coin[pie][circulation] = $coin[total] - $coin[pie][unused];
$coin[pie][ico] = dataGet([$domain, token, ico, amount]);
$coin[pie][bonus] = dataGet([$domain, token, bonus, amount]);

/*$coin[logo] = dataGet([wallet, info, $domain, logo]);

$coin[pie][deligated] = dataGet([$domain, token, $coin[owner], script]);

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