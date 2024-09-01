<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/constructor/api/utils.php";

$domain1 = get_required(domain1);
$pass1 = get_required(pass1);
$amount1 = get_required(amount1);

$domain2 = get_required(domain2);
$pass2 = get_required(pass2);
$amount2 = get_required(amount2);

$domainResult = get_required(domainResult);
$passResult = get_required(passResult);
$amountResult = get_required(amountResult);

$interval = get_required(interval);