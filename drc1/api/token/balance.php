<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$address = get_required(address);

description("Get the balance of a token.");

$response = dataWalletBalance(getDomain(), $address);

commit($response);
