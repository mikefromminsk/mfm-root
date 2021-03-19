<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/auth.php";

$domain_name = get_required_uppercase("domain_name");

description(basename(__FILE__));

$response["coin"] = dataGet(["coins", $domain_name], $pass);

echo json_encode($response);