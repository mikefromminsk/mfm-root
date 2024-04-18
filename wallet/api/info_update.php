<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);
$password = get_required(password);
$next_hash = get_required(next_hash);
$title = get_required(title);
$gas_address = get_required(gas_address);
$hide_in_store = get_required(hide_in_store);

dataSet([wallet, info, $domain], [
    title => $title,
    owner => $gas_address,
    hide_in_store => $hide_in_store,
]);

/*$dir = $_SERVER["DOCUMENT_ROOT"];
foreach (scandir($dir) as $key => $value) {
    if ($value[0] != "." && is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
        $response[result][] = [
            domain => $value,
            hash => dataGet([$value, hash]),
            next_hash => dataGet([$value, next_hash]),
        ];
    }
}*/


$response[success] = true;
commit($response);