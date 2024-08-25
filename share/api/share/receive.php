<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/token/utils.php";

$invite_key = get_required(invite_key);
$to_address = get_required(to_address);

$domain = getDomain();

$invite_hash = md5($invite_key);
$amount = dataGet([$domain, share, $invite_hash, amount]);

if ($amount == null) error("hash is not right");
if ($amount == 0) error("share finished");

tokenSend($domain, share, $to_address, $amount);

dataSet([$domain, share, $invite_hash, amount], "0");

$response[received] = $amount;

commit($response);
