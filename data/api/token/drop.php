<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$address = get_required(address);

$response[success] = dataWalletSend("data/wallet", gas_giveaway, $address, 10000);

commit($response, gas_giveaway);