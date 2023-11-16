<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domains = get_required(domains);
$address = get_string(address);

$domains = explode(",", $domains);

$response[result] = [];

function getTokenData($domain, $address = null){
    $path = dataGet([wallet, info, $domain, path]);
    return [
        domain => $domain,
        logo => "/wallet/img/coin.svg",
        path => $path,
        price => dataGet([$domain, price]) ?: 0,
        balance => $address != null ? dataWalletBalance($path, $address) : null,
    ];
}

foreach ($domains as $domain) {
    if ($domain == null) continue;
    $coin = getTokenData($domain, $address);
    $response[result][] = $coin;
}

$response[gas] = getTokenData(data, $address);

commit($response);