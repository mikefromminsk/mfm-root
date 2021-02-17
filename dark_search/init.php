<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/init.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";

$prefix = get_required("prefix");
$count = get_int_required("count");

$tokens = [];

for ($i = 0; $i < $count; $i++) {
    $domain_name = $prefix . $i;
    $password = random_id();
    $tokens[$domain_name] = $password;
    domain_put($domain_name, null, $password, null);
}

$response["tokens"] = $tokens;

echo json_encode_readable($response);