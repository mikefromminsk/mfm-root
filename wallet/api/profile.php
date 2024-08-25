<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/track.php";

$domain = get_required(domain);
$address = get_string(address);

$token[domain] = $domain;
$token[title] = dataGet([wallet, info, $domain, title]);
$token[hide_in_store] = dataGet([wallet, info, $domain, hide_in_store]) == 1;
$token[total] = dataGet([wallet, info, $domain, total]);
$token[owner] = getTokenOwner($domain);
$token[trans] = dataCount([$domain, trans]);
$token[wallets] = dataCount([$domain, token]);
$token[nodes] = 1;
$token[created] = dataInfo([$domain])[data_time];
$token[dapps] = [];
$token[ui] = dataGet([wallet, info, $domain, ui]);
$token[mining] = dataExist([$domain, mining]);
$token[description] = dataGet([wallet, info, $domain, description]);
$token[ico_balance] = tokenAddressBalance($domain, ico);
$token[gas_balance] = tokenAddressBalance($gas_domain, $address);


$token[price] = dataGet([$domain, price]);
$token[price24] = getCandleChange24($domain, price);
/*$token[volume] = 100;
$token[wallets] = 100;
$token[transfers] = 100;
$token[volume] = 100;*/
$token[mcap] = $token[total] * $token[price];


foreach (dataKeys([$domain, packages]) as $app_domain) {
    $token[dapps][$app_domain] = [
        hash => dataGet([$domain, packages, $app_domain, hash]),
    ];
}

$token[pie][blocked] = 0;
$token[pie][unused] = dataGet([$domain, token, $token[owner], amount]);
if (dataGet([$domain, token, $token[owner], script])) {
    $token[pie][unused] = 0;
    $token[pie][blocked] = $token[pie][unused];
}/*
$token[pie][circulation] = $token[total] - $token[pie][unused];
$token[pie][ico] = dataGet([$domain, token, ico, amount]);
$token[pie][bonus] = dataGet([$domain, token, bonus, amount]);

$coin[logo] = dataGet([wallet, info, $domain, logo]);

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

commit($token);