<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$path = get_path_required(path);
$address = get_required(address);
$next_hash = get_required(next_hash);
$amount = get_int_required(amount);

$response[success] = dataWalletInit($path, $address, $next_hash, $amount);

commit($response);