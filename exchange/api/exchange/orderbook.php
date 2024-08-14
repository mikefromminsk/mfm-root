<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/exchange/api/exchange/utils.php";

$response = getOrderbook(getDomain());

commit($response);