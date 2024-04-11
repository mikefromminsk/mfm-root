<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domains = get_required(domains);
$address = get_string(address);

$response[result] = [];

$response[gas] = dataWalletProfile($gas_domain, $address);

if ($domains != "") {
    $domains = explode(",", $domains);

    foreach ($domains as $domain)
        $response[result][] = dataWalletProfile($domain, $address);

    if ($address != null)
        usort($response[result], function ($a, $b) {
            $balance = -strcmp($a[balance] * $a[price], $b[balance] * $b[price]);
            return $balance != 0 ? $balance : ($b[created] - $a[created]);
        });
}

commit($response);