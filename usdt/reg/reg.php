<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt/utils.php";

$address = get_required(address);
$next_hash = get_required(next_hash);

$response[success] = dataWalletReg([usdt, wallet], $address, $next_hash);

commit($response, usdt_reg);