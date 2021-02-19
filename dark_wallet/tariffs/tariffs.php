<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/properties.php";

$response["tariffs"] = $tariffs;
$response["pot_prices"] = $rates;

echo json_encode($response);