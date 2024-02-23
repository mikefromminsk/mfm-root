<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$address = get_required(address);
$next_hash = get_required(next_hash);

$domain = getDomain();

$response[success] = dataWalletReg($address, $next_hash);

commit($response, $domain . "_reg");