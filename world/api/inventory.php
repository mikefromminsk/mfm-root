<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$address = get_required(address);

$response = [];
foreach (dataKeys([world, avatar, $address, inventory]) as $domain) {
    $amount = dataGet([world, avatar, $address, inventory, $domain, amount]);
    $response[inventory][] = [
        domain => $domain,
        amount => $amount ?: 1
    ];
}

commit($response);