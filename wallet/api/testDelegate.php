<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

dataSet([hello], "Hello world!!!");
$response[success] = dataExist([hello]);

/*error(dataWalletBalance([data, wallet], user1));
error($GLOBALS["gas_bytes"]);*/
commit($response, user1);