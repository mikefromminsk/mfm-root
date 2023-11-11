<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$address = get_required(address);
$key = get_required(key);
$next_hash = get_required(next_hash);
$amount = get_int_required(amount);

dataIcoBuy($address, $key, $next_hash, $amount);

$response[success] = true;

commit($response);