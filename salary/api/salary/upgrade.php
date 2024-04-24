<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$employee_address = get_required(employee_address);
$manager_address = get_required(manager_address);
$block_distance = get_int_required(block_distance);
$amount = get_int_required(amount);

$domain = getDomain();


if (!dataExist([$domain, wallet, $employee_address])) error("Employee not found");
if (!dataExist([$domain, wallet, $manager_address])) error("Manager not found");

dataSet([$domain, salary, $employee_address], [
    amount => $amount,
    block_distance => $block_distance,
    manager_address => $manager_address,
]);

$response[success] = true;
commit($response);