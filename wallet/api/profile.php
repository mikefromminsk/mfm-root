<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/track.php";

$domain = get_required(domain);
$address = get_string(address);

$token[domain] = $domain;
$token[title] = dataGet([wallet, info, $domain, title]);
$token[hide_in_store] = dataGet([wallet, info, $domain, hide_in_store]) == 1;
$token[nodes] = 1;
$tran = tokenFirstTran($domain);
$token[created] = $tran[time];
$token[total] = $tran[amount];
$token[owner] = $tran[to];
$token[circulation] = $tran[amount] - tokenAddressBalance($domain, $token[owner]);

$token[description] = dataGet([wallet, info, $domain, description]);
$token[balance] = tokenAddressBalance($domain, $address);
$token[price] = getCandleLastValue($domain . _price);
$token[price24] = getCandleChange24($domain . _price);
$token[trans] = getCandleLastValue($domain . _trans);
$token[addresses] = getCandleLastValue($domain . _addresses);
$token[volume] = getCandleLastValue($domain . _volume);


$token[mcap] = $token[total] * $token[price];


$token[dapps] = [];
foreach (dataKeys([wallet, $domain, packages]) as $app_domain) {
    $token[dapps][$app_domain] = [
        hash => dataGet([wallet, $domain, packages, $app_domain, hash]),
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