<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/token/utils.php";

$domain = get_required(domain);
$address = get_required(address);
$next_hash = get_required(next_hash);

tokenAddressReg($domain, $address, $next_hash);

$response[success] = true;

commit($response);