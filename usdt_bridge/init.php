<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

tokenScriptReg(usdt,usdt_deposits, "usdt/api/deposit/check.php");
tokenScriptReg(usdt,usdt_deposit_check, "usdt/api/deposit/check.php");
tokenScriptReg(usdt,usdt_deposit_start, "usdt/api/deposit/start.php");
tokenScriptReg(usdt,usdt_withdrawals, "usdt/api/withdrawal/success.php");
tokenScriptReg(usdt,usdt_withdrawal_success, "usdt/api/withdrawal/success.php");
tokenScriptReg(usdt,usdt_withdrawal_start, "usdt/api/withdrawal/start.php");
commit();