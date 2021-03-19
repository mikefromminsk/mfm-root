<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/utils.php";

$domain_name = get_required("domain_name");
$domain_name_postfix_length = get_required("domain_name_postfix_length");

description("generate coin");

$response = domains_generate_keys($domain_name, $domain_name_postfix_length);

echo json_encode($response);