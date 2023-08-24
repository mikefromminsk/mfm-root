<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$address = get_required(address);

$response = dataWalletBalance([usdt, wallet], $address);

echo json_encode($response);
