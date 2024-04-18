<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);
$file = get_required(file);

$response[file_hash] = installApp($domain, $domain, $file[tmp_name]);

$response[success] = true;
commit($response);
