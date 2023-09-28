<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$address = get_required(address);
$next_hash = get_required(next_hash);

$response[success] = dataWalletReg([data, wallet], $address, $next_hash);

commit($response, reg);