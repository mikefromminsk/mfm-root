<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domains = get_required(domains);
$address = get_string(address);

$domains = explode(",", $domains);

$response[result] = [];

foreach ($domains as $domain) {
    if ($domain == null) continue;
    $coin = [
        domain => $domain,
        path => dataGet([wallet, info, $domain, path]),
        price => dataGet([$domain, price]) ?: 13.05,
    ];
    if ($address != null)
        $coin[balance] = dataWalletBalance($coin[path], $address);
    $response[result][] = $coin;
}

commit($response);