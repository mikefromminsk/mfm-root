<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$response = [];
$dir = $_SERVER["DOCUMENT_ROOT"];
foreach (scandir($dir) as $key => $value) {
    if ($value[0] != "." && is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
        $response[result][] = [
            domain => $value,
            hash => dataGet([$value, hash]),
            next_hash => dataGet([$value, next_hash]),
        ];
    }
}

commit($response);