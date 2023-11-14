<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$invite_id = get_required(invite_id);
$to_address = get_string(to_address);
$invite_key = get_string(invite_key);
$cancel_key = get_string(cancel_key);

if ($invite_key == null || $cancel_key = null) error("all hashes are null");

if ($invite_key != null){
    $response[success] = dataWalletBonusRecieve(
        getDomain(),
        $to_address,
        $invite_id,
        $invite_key);
} else if ($cancel_key != null) {
    $response[success] = dataWalletBonusCancel(
        getDomain(),
        $invite_id,
        $invite_key);
}

commit($response);
