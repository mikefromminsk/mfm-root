<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$address = get_required(address);
$next_hash = get_required(next_hash);

$response[success] = dataWalletReg(getDomain() . "/wallet", $address, $next_hash);

commit($response);