<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/properties.php";

$token = get_required("token");

$login = dataGet("tokens", $token, $admin_password);

if ($login == null)
    error("login is not exist");

