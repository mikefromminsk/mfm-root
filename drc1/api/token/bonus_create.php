<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$gas_address = get_required(gas_address);
$key = get_required(key);
$next_hash = get_required(next_hash);
$amount = get_int_required(amount);
$invite_next_hash = get_required(invite_next_hash);

$response[success] = dataWalletBonusCreate(
    getDomain(),
    $gas_address,
    $key,
    $next_hash,
    $amount,
    $invite_next_hash);

commit($response);
