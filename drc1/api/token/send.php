<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$from_address = get_required(from_address);
$to_address = get_required(to_address);
$password = get_required(password);
$next_hash = get_required(next_hash);
$amount = get_int_required(amount);

$response[next_trans] = dataWalletSend(getDomain(), $from_address, $to_address, $amount, $password, $next_hash);
$response[success] = true;

commit($response);
