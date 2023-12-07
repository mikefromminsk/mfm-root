<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$invite_key = get_required(invite_key);
$to_address = get_required(to_address);

$response[received] = dataWalletBonusRecieve(getDomain(), $invite_key, $to_address);

commit($response);
