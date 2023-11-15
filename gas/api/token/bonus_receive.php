<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$invite_id = get_required(invite_id);
$to_address = get_required(to_address);
$key = get_required(key);

$response[success] = dataWalletBonusRecieve(
    getDomain(),
    $to_address,
    $invite_id,
    $key);

commit($response);
