<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domains = get_required(domains);
$address = get_string(address);

$domains = explode(",", $domains);

$response[result] = [];

foreach ($domains as $domain) {
    $path = dataGet([wallet, info, $domain, path]);
    $response[result][] = [
        domain => $domain,
        logo => "/wallet/img/coin.svg",
        path => $path,
        price => dataGet([$domain, price]) ?: 0,
        balance => $address != null ? dataWalletBalance($path, $address) : null,
    ];
}

commit($response);