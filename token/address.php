<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/token/utils.php";

$domain = get_required(domain);
$address = get_required(address);

$response = tokenAddress($domain, $address);

if (!$response) {
    error("Address not found");
}

commit($response);
