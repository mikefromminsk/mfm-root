<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/darknode/domain_utils.php";

$domain_name = get_required("domain_name");

echo json_encode(domain_similar($domain_name));