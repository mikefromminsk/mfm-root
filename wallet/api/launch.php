<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

if (!DEBUG) error("cannot use in debug session");

$domain = get_required(domain);
$contract = get_required(contract, "gas");

http_post_json($GLOBALS[host_name] . "/wallet/contracts/archive.php", [
    domain => "gas",
]);

$response[files] = upload($domain, $_SERVER["DOCUMENT_ROOT"] . "/wallet/contracts/$contract.zip");

$response[success] = true;

commit($response);
