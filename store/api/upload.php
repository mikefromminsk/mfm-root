<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

// нужен второй сервер для синхронизации

$domain = get_required(domain);
$file = get_required(file);

upload($domain, $file[tmp_name]);

// spend gas tokens

$response[success] = true;
commit($response);
