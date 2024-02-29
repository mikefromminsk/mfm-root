<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = getDomain();

$response[info] = [
    domain => $domain,
    logo => dataGet([wallet, info, $domain, logo]),
    owner => dataGet([wallet, info, $domain, owner]),
    price => dataGet([$domain, price]) ?: 0,
    category => dataGet([wallet, info, $domain, category]) ?: UNKNOWN,
    trans => dataCount([$domain, trans]),
    wallets => dataCount([$domain, wallet]),
    price24hChange => 0,
];

commit($response);