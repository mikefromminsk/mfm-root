<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock0/auth.php";

$domain_name = get_required_uppercase("domain_name");

description(basename(__FILE__));

$response["pairs"] = dataLike(["pairs"], $pass, "%$domain_name%");

echo json_encode($response);