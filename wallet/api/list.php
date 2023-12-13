<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domains = get_string(domains);
$search_text = get_string(search_text);
$address = get_string(address);

if ($domains != null){
    $domains = explode(",", $domains);
} else {
    $domains = dataSearch("wallet/info", $search_text) ?: [];
}

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

usort($response[result], function($a, $b)
{
    return - strcmp($a[balance] * $a[price], $b[balance] * $b[price]);
});


commit($response);