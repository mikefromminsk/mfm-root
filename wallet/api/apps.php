<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$search_text = get_string(search_text, "");
$domain = get_string(domain);
//$category = get_required(category);

$domains = implode(",",    dataSearch("wallet/info", $search_text) ?: []);

$apps = [];

if ($domains != "") {
    $domains = explode(",", $domains);
    foreach ($domains as $appDomain) {
        if (dataGet([wallet, info, $appDomain, title]) == null) continue;
        $apps[] = [
            domain => $appDomain,
            title => dataGet([wallet, info, $appDomain, title]),
            description => dataGet([wallet, info, $appDomain, description]),
            logo => dataGet([wallet, info, $appDomain, logo]),
            preview => dataGet([wallet, info, $appDomain, preview]),
            owner => dataGet([wallet, info, $appDomain, owner]),
            category => dataGet([wallet, info, $appDomain, ui]) == 1 ? Website : Plugin,
            installed => dataExist([$domain, packages, $appDomain, hash]),
            console => dataGet([wallet, info, $appDomain, console]) == 1,
            hide_in_store => dataGet([wallet, info, $appDomain, hide_in_store]) == 1,
            uptodate => dataGet([$domain, packages, $appDomain, hash]) == dataGet([$appDomain, packages, $appDomain, hash]),
        ];
    }
}

$response[apps] = $apps;

commit($response);