<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/domain_utils.php";

$domains = get_required("domains");

echo json_encode_readable(domains_set($host_name, $domains));

