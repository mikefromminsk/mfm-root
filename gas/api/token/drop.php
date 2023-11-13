<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$address = get_required(address);

$domain = getDomain();

$response[success] = dataWalletSend("$domain/wallet", $domain . "_drop", $address, 10000);

commit($response, $domain . "_drop");