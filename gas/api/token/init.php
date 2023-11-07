<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$response[success] = dataWalletInit(getDomain() . "/wallet", user, md5(pass), 1000000.0);

commit($response);