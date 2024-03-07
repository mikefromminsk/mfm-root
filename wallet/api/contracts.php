<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);

description("Get contracts");

foreach (dataKeys([store, info, $domain, contracts]) as $hash) {
    $response[contracts][$hash] = dataGet([store, info, $domain, contracts, $hash]);
}

commit($response);