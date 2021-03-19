<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/utils.php";

$token = get_required("token");

$login = dataGet(["tokens", $token], $pass);

if ($login == null)
    error("login is not exist");

