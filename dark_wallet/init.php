<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/init.php";

$keys = requestEquals("localhost/dark_domain/hosting.php",
    array(
        "domain_name" => "PAIN",
        "domain_postfix_length" => 1,
        "keys" => $payment_keys,
    ), "errors", 0);

requestCount("localhost/dark_domain/domains.php",
    array(
        "domains" => $pain["domains"]
    ), "bad_domains", 0);


