<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domains = get_required(domains);
$address = get_string(address);

$response[result] = [];

if ($domains != "") {
    $domains = explode(",", $domains);

    foreach ($domains as $domain) {
        $response[result][] = [
            domain => $domain,
            logo => dataGet([wallet, info, $domain, logo]),
            owner => dataGet([wallet, info, $domain, owner]),
            price => dataGet([$domain, price]) ?: 0,
            category => dataGet([wallet, info, $domain, category]) ?: UNKNOWN,
            price24hChange => 0,
            balance => $address != null ? dataWalletBalance($domain, $address) : null,
        ];
    }

    if ($address != null)
        usort($response[result], function ($a, $b) {
            return -strcmp($a[balance] * $a[price], $b[balance] * $b[price]);
        });
}

commit($response);