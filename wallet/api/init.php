<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/init.php";

$keys = requestEquals("localhost/domains/api/hosting.php",
    array(
        "domain_name" => "PAIN",
        "domain_postfix_length" => 1,
        "keys" => $payment_keys,
    ), "errors", 0);

requestCount("localhost/domains/api/domains.php",
    array(
        "domains" => $pain["domains"]
    ), "bad_domains", 0);


