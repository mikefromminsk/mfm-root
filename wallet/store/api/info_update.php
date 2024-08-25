<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);
$title = get_required(title);
$hide_in_store = get_required(hide_in_store);
$gas_address = get_required(gas_address);

dataSet([wallet, $domain], [
    title => $title,
    owner => $gas_address,
    hide_in_store => $hide_in_store,
]);

$response[success] = true;
commit($response);