<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = getDomain();

dataWalletReg($domain . "_mining", md5(pass));
dataWalletDelegate($domain, $domain . "_mining", pass, "$domain/api/mint.php");

$response[success] = true;

commit($response);