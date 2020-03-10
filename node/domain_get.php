<?php
// 404 if not found and send similar
include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$domain_name = get_required("domain_name");

$domain = domain_get($domain_name);

if ($domain == null)
    error("domain is not exist");

echo json_encode($domain);