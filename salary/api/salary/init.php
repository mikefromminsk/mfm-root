<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = getDomain();

dataWalletRegScript($domain,  "salary", "$domain/api/salary/approve.php");

$response[success] = true;

commit($response);