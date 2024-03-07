<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = getDomain();

dataWalletReg("mining", md5(pass));
dataWalletDelegate($domain,  "mining", pass, "$domain/api/mining/mint.php");

$response[success] = true;

commit($response);