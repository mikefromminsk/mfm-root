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
            mining => dataExist([$domain, mining]),
        ];
    }

    if ($address != null)
        usort($response[result], function ($a, $b) {
            $balance = -strcmp($a[balance] * $a[price], $b[balance] * $b[price]);
            return $balance != 0 ? $balance : strcmp($a[domain], $b[domain]);
        });
}

commit($response);