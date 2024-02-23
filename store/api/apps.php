<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$search_text = get_string(search_text, "");
//$category = get_required(category);

$domains = implode(",",    dataSearch("store/info", $search_text) ?: []);

$apps = [];

if ($domains != "") {
    $domains = explode(",", $domains);
    foreach ($domains as $domain) {
        $apps[] = [
            domain => $domain,
            title => dataGet([store, info, $domain, title]),
            description => dataGet([store, info, $domain, description]),
            logo => dataGet([store, info, $domain, logo]),
            preview => dataGet([store, info, $domain, preview]),
            owner => dataGet([store, info, $domain, owner]),
            category => dataGet([store, info, $domain, category]) ?: UNKNOWN,
        ];
    }
}

$response[apps] = array_to_map($apps, domain);
foreach ($apps as $app)
    $response[categories][$app[category]][] = $app[domain];


commit($response);