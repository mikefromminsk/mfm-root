<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$address = get_required(address);
$password = get_required(password);
$script = get_required(script);

$response[success] = dataWalletDelegate([data, wallet], $address, $password, $script);

commit($response);