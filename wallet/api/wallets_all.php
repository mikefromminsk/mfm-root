<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_string(domain);


$keys = dataKeys([$domain, token], 1, 10000);

$response = [];

foreach ($keys as $key) {
    $response[] = dataGet([$domain, token, $key, amount]);
}

commit($response);
