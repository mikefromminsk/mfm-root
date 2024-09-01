<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/track.php";

$address = get_string(address);
$search_text = get_string(search_text);

$response[result] = [];

function dataWalletProfile($domain, $address = null)
{
    return [
        domain => $domain,
        price => getCandleLastValue($domain . _price),
        price24 => getCandleChange24($domain . _price),
        balance => tokenAddressBalance($domain, $address),
    ];
}

if ($address != null) {
    $active_domains = selectList("select `domain` from addresses where `address` = '$address' "
        . " and `domain` like '$search_text%'"
    );
    foreach ($active_domains as $domain)
        $response[active][] = dataWalletProfile($domain, $address);

    $domains = selectList("select distinct `domain` from addresses "
        . " where `domain` not in (select `domain` from addresses where `address` = '$address') "
        . " and `domain` like '$search_text%'"
        . " limit 0, 20");
    foreach ($domains as $domain)
        $response[recs][] = dataWalletProfile($domain, $address);

    usort($response[active], function ($a, $b) {
        return -strcmp($a[balance] * $a[price], $b[balance] * $b[price]);
    });
} else {
    $domains = selectList("select distinct `domain` from addresses"
        . " where `domain` like '$search_text%'"
        . " limit 0, 20");
    foreach ($domains as $domain)
        $response[recs][] = dataWalletProfile($domain, $address);
}

commit($response);