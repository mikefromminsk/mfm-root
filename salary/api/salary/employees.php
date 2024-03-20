<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$search_text = get_string(search_text, "");
$domain = getDomain();

$employee_addresses = dataSearch([$domain, salary], $search_text) ?: [];

$employees = [];

foreach ($employee_addresses as $address) {
    $employees[] = [
        employee_address => $address,
        amount => dataGet([$domain, salary, $address, amount]),
        block_distance => dataGet([$domain, salary, $address, block_distance]),
        manager_address => dataGet([$domain, salary, $address, manager_address]),
        last_approve_block => dataGet([$domain, salary, $address, last_approve_block]) ?: 0,
    ];
}
$response[employees] = $employees;
$response[balance] = dataWalletBalance($domain, salary);

commit($response);