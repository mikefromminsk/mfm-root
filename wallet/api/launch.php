<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);
$contract = get_required(contract, "data10");

$response[files] = upload($domain, $_SERVER["DOCUMENT_ROOT"] . "/wallet/contracts/$contract.zip");

$response[success] = true;

commit($response);
