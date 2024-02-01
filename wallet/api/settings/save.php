<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$user = get_required(user);
$key = get_required(key);
$value = get_required(value);

$response[success] = dataWalletSettingsSave($user, $key, $value);

commit($response, wallet_settings);