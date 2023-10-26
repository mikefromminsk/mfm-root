<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$from_address = get_required(from_address);
$to_address = get_required(to_address);
$password = get_required(password);
$next_hash = get_required(next_hash);
$amount = get_int_required(amount);
$wallet_path = get_path(wallet_path, "data/wallet");

$response[success] = dataWalletSend($wallet_path, $from_address, $to_address, $amount, $password, $next_hash);

commit($response);
