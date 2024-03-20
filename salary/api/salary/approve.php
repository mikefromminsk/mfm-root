<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$employee_address = get_required(employee_address);
$gas_address = get_required(gas_address);

$domain = getDomain();

$block_distance = dataGet([$domain, salary, $employee_address, block_distance]);
$manager_address = dataGet([$domain, salary, $employee_address, manager_address]);
$last_approve_block = dataGet([$domain, salary, $employee_address, last_approve_block]) ?: 0;
$amount = dataGet([$domain, salary, $employee_address, amount]);
$current_block = dataWalletBlocks();

if ($gas_address != $manager_address) error("Only manager can approve salary");
if ($current_block - $last_approve_block < $block_distance) error("Block distance is not expired");

dataWalletSend($domain, salary, $employee_address, $amount);
dataSet([$domain, salary, $employee_address, last_approve_block], $current_block);

$response[success] = true;
commit($response);