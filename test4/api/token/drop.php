<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$address = get_required(address);

$domain = getDomain();

$response[dropped] = 50000;
$response[success] = dataWalletSend($domain, $domain . "_drop", $address, $response[dropped]);

commit($response, $domain . "_drop");