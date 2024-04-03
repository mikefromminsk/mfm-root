<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);
$address = get_required(address);
$key = get_required(key);
$next_hash = get_required(next_hash);

if (!DEBUG) error("cannot use not in debug session");

$prev_key = dataGet([$domain, wallet, $address, prev_key]);

if (md5($key) != dataGet([$domain, wallet, $address, next_hash])) error("cannot change password");

dataSet([$domain, wallet, $address, prev_key], "");
dataSet([$domain, wallet, $address, next_hash], $next_hash);

$response[success] = true;

commit($response, change_pass);