<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";

$domains = get_required("domains");

$response = domains_set($host_name, $domains);

echo json_encode_readable($response);

