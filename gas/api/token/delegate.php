<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$address = get_required(address);
$password = get_required(password);
$script = get_required(script);

$response[success] = dataWalletDelegate(getDomain() . "/wallet", $address, $password, $script);

include_once "free_commit.php";