<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock0/auth.php";

$domain_name = get_required_uppercase("domain_name");

description(basename(__FILE__));

$promo_codes = dataGet(["users", $login, "promos", $domain_name], $pass);

$response["promos"] = $promo_codes;

echo json_encode($response);