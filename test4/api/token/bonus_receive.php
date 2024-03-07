<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$invite_key = get_required(invite_key);
$gas_address = get_required(gas_address);

$response[received] = dataWalletBonusReceive(getDomain(), $invite_key, $gas_address);

commit($response);
