<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$fromAddress = get_required(fromAddress);
$toAddress = get_required(toAddress);
$password = get_required(password);
$next_hash = get_required(next_hash);
$amount = get_int_required(amount);

$response[success] = dataWalletSend([data, wallet], $fromAddress, $toAddress, $amount, $password, $next_hash);

commit($response);
