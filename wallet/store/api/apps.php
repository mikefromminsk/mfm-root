<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$search_text = get_string(search_text, "");
$domain = get_string(domain);

$domains = dataSearch(wallet, $search_text) ?: [];

$apps = [];
foreach ($domains as $appDomain) {
    if (dataGet([wallet, $appDomain, title]) == null) continue;
    $apps[] = [
        domain => $appDomain,
        title => dataGet([wallet, $appDomain, title]),
        installed => dataExist([wallet, $domain, packages, $appDomain, hash]),
        hide_in_store => dataGet([wallet, $domain, $appDomain, hide_in_store]) == 1,
        hash => dataGet([wallet, $domain, packages, $appDomain, hash]),
        apphash => dataGet([wallet, $appDomain, packages, $appDomain, hash]),
        appDomain => $appDomain,
        uptodate => dataGet([wallet, $domain, packages, $appDomain, hash]) == dataGet([wallet, $appDomain, packages, $appDomain, hash]),
    ];
}

$response[apps] = $apps;

commit($response);