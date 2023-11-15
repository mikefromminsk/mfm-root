<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$from_address = get_required(from_address);
$from_key = get_required(from_key);
$from_next_hash = get_required(from_next_hash);
$amount = get_int_required(amount);
$invite_hash = get_required(invite_hash);

$domain = getDomain();

$response[success] = dataWalletBonusCreate(
    $domain,
    $from_address,
    $from_key,
    $from_next_hash,
    $amount,
    $invite_hash);

commit($response);
