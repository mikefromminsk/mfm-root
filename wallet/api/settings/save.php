<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$user = get_required(user);
$key = get_required(key);
$value = get_required(value);

$values = dataHistory([wallet, settings, $user, $key]) ?: [];

if (array_search($value, $values) === false)
    dataSet([wallet, settings, $user, $key], $value);

$response[success] = dataGet([wallet, settings, $user, $key]) == $value;

commit($response, wallet_settings);