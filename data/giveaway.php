<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$address = get_required(address);

$response[success] = dataWalletSend([data, wallet], gas_giveaway, $address, 500);

commit($response, gas_giveaway);