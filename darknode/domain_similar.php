<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/darknode/domain_utils.php";

$domain_name = get_required("domain_name");

echo json_encode(array(
    "domain" => domain_get($domain_name),
    "similar" => domain_similar($domain_name)
));