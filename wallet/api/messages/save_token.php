<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$gas_address = get_required(gas_address);
$token = get_required(token);

$response[success] = false;

if (dataGet([wallet, tokens, $gas_address]) != $token)
    $response[success] = dataSet([wallet, tokens, $gas_address], $token);
else
    $response[success] = true;

commit($response);