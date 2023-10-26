<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domains = get_required(domains);
$address = get_string(address);

$domains = explode(",", $domains);

foreach ($domains as $domain) {
    if ($domain == null) continue;
    $wallet_path = dataGet([wallet, info, $domain, path]);
    $coin = [
        domain => $domain,
        path => $wallet_path,
    ];
    if ($address != null)
        $coin[balance] = dataWalletBalance($wallet_path, $address);
    $response[result][] = $coin;
}

if ($response[result] == null) error("nothing found");

commit($response);