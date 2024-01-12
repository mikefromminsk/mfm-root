<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$user = get_required(user);
$key = get_required(key);

$response[settings] = dataHistory([wallet, settings, $user, $key]) ?: [];

commit($response);