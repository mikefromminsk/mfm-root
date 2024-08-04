<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$gas_address = get_required(gas_address);
$key = get_required(key);
$next_hash = get_required(next_hash);
$amount = get_int_required(amount);
$invite_next_hash = get_required(invite_next_hash);

$domain = getDomain();

if (dataExist([$domain, invite, $invite_next_hash])) error("bonus exist");

dataWalletRegScript($domain, bonus, "$domain/api/token/invite/receive.php");

dataWalletSend($domain, $gas_address, bonus, $amount, $key, $next_hash);

dataSet([$domain, bonus, $invite_next_hash, amount], $amount);

$response[success] = true;

commit($response);
