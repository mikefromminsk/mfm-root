<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/utils.php";

$token = get_required("token");

$login = dataGet(["tokens", $token], $admin_token);

if ($login == null)
    error("login is not exist");