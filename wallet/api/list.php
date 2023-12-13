<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domains = get_required(domains);
$address = get_string(address);

$domains = explode(",", $domains);

$response[result] = [];

foreach ($domains as $domain) {
    $response[result][] = [
        domain => $domain,
        logo => dataGet([wallet, info, $domain, logo]),
        owner => dataGet([wallet, info, $domain, owner]),
        price => dataGet([$domain, price]) ?: 0,
        price24hChange => 0,
        balance => $address != null ? dataWalletBalance($domain, $address) : null,
    ];
}

usort($response[result], function ($a, $b) {
    return -strcmp($a[balance] * $a[price], $b[balance] * $b[price]);
});


commit($response);