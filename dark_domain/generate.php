<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";

$domain_name = get_required("domain_name");
$domain_name_postfix_length = get_required("domain_name_postfix_length");

description("generate coin");

$response = domains_generate($domain_name, $domain_name_postfix_length);

echo json_encode($response);