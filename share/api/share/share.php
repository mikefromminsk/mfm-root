<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/token/utils.php";

$gas_address = get_required(gas_address);
$pass = get_required(pass);
$amount = get_int_required(amount);
$invite_next_hash = get_required(invite_next_hash);

$domain = getDomain();

if (dataExist([$domain, invite, $invite_next_hash])) error("bonus exist");

tokenScriptReg($domain, share, "$domain/api/share/receive.php");

tokenSend($domain, $gas_address, share, $amount, $pass);

dataSet([$domain, share, $invite_next_hash, amount], $amount);

$response[success] = true;

commit($response);
