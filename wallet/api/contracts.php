<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);

foreach (dataKeys([store, $domain]) as $hash) {
    $response[contracts][$hash] = dataGet([store, $domain, $hash]);
}

commit($response);