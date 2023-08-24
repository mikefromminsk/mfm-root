<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$address = get_required(address);
$password = get_required(password);
$owner = get_required(owner);

$response[success] = dataWalletDelegate([data, wallet], $address, $password, $owner);

commit($response, usdt);