<?php

include_once "utils.php";

$response = getOrderbook(getDomain());

commit($response);