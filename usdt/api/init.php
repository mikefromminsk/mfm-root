<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

dataWalletRegScript(usdt,usdt_deposits, "usdt/api/deposit/check.php");
dataWalletRegScript($gas_domain,usdt_deposit_check, "usdt/api/deposit/check.php");
dataWalletRegScript($gas_domain,usdt_deposit_start, "usdt/api/deposit/start.php");
dataWalletRegScript(usdt,usdt_withdrawals, "usdt/api/withdrawal/success.php");
dataWalletRegScript($gas_domain,usdt_withdrawal_success, "usdt/api/withdrawal/success.php");
dataWalletRegScript($gas_domain,usdt_withdrawal_start, "usdt/api/withdrawal/start.php");

$response[success] = true;

commit($response);
