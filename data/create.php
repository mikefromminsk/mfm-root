<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$path = get_required(path);
$address = get_required(address);
$next_hash = get_required(next_hash);
$amount = get_required(amount);

$path = explode("/", $path);

$response[wallet] = dataWalletInit($path, $address, $next_hash, $amount);
$response[success] = true;

commit($response);