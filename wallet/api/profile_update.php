<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);
$description = get_string(description);

if ($description != null){
    if (strlen($description) > 1000) error("Description is too long");
    if (strlen($description) < 10) error("Description is too short");
    dataSet([wallet, info, $domain, description], $description);
}

$response[success] = true;

commit($response);