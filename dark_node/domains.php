<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/utils.php";

$domains = get_required("domains");

echo json_encode_readable(domains_set($host_name, $domains));

