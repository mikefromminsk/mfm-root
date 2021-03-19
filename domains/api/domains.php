<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/utils.php";

$domains = get_required("domains");

description("set domains");

$response["bad_domains"] = domains_set($host_name, $domains);

echo json_encode($response);

