<?php

include_once "domain_utils.php";

$domains = get_required("domains");

echo json_encode_readable(domains_set($host_name, $domains));

