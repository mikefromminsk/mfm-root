<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

if (!DEBUG) error("cannot use in debug session");

$domain = get_required(domain);
$address = get_required(address);
$next_hash = get_required(next_hash);
$amount = get_int_required(amount);
$contract = get_required(contract, "gas");

$wallet_path = $domain . "/wallet";

if (strlen($domain) < 3 || strlen($domain) > 16) error("domain length has to be between 3 and 16");
if (dataExist($wallet_path)) error("path $wallet_path exist");

http_post($GLOBALS[host_name] . "/wallet/contracts/archive.php", [
    domain => $contract,
]);

$response[files] = upload($domain, $_SERVER["DOCUMENT_ROOT"] . "/wallet/contracts/$contract.zip");

dataWalletReg($address, $next_hash, $domain);
dataSet([$wallet_path, $address, amount], $amount);

dataSet([wallet, info, $domain], [
    domain => $domain,
    owner => $address,
    total => $amount,
]);

$response[success] = true;
$response[launched] = dataGet([$wallet_path, $address, amount]);

commit($response);
