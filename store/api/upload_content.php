<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);
$file = get_required(file);

uploadContent($domain, $file[tmp_name], $domain);

$response[success] = true;
commit($response);
