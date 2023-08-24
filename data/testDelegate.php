<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$response[success] = dataSet([user1], "Hello world!!!");

/*error(dataWalletBalance([data, wallet], user1));
error($GLOBALS["gas_bytes"]);*/
commit($response, user1);