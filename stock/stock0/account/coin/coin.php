<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock0/auth.php";

$domain_name = get_required_uppercase("domain_name");

description(basename(__FILE__));

$response["domain_name"] = $domain_name;
$response["count"] = dataGet(["users", $login, "balance", $domain_name], $pass);

echo json_encode($response);