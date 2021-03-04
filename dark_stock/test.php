<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/test.php";

$admin_token = requestNotEquals("localhost/dark_stock/reg.php",
    array(
        "login" => "admin",
        "password" => "123",
    ),"token", null)["token"];


$admin_token = requestNotEquals("localhost/dark_stock/limit.php",
    array(
        "login" => "admin",
        "password" => "123",
    ),"token", null)["token"];

