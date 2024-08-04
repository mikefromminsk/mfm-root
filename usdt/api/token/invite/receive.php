<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$invite_key = get_required(invite_key);
$to_address = get_required(to_address);

$domain = getDomain();

$invite_hash = md5($invite_key);
$amount = dataGet([$domain, bonus, $invite_hash, amount]);

if ($amount == null) error("hash is not right");
if ($amount == 0) error("bonus finished");

dataWalletSend($domain, bonus, $to_address, $amount);

dataSet([$domain, bonus, $invite_hash, amount], "0");

$response[received] = $amount;

commit($response);
