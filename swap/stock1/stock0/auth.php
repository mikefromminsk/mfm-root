<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock0/utils.php";

$token = get_required("token");

$login = dataGet(["tokens", $token], $pass);

if ($login == null)
    error("login is not exist");

