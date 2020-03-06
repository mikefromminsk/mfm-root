<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$domain_name = get_required("domain_name");
$path = get("path");
$domain_key = get("domain_key");

file_delete($domain_name, $path, $domain_key);


