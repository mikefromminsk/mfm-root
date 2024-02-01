<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$user = get_required(user);
$key = get_required(key);

$response[settings] = dataWalletSettingsRead($user, $key);

commit($response);