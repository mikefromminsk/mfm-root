<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/test.php";

$admin_token = requestNotEquals("localhost/dark_stock/reg.php",
    array(
        "login" => "admin",
        "password" => "123",
    ),"token", null)["token"];



$admin_token = requestNotEquals("localhost/dark_stock/limit.php",
    array(
        "token" => $admin_token,
        "from_domain_name" => "TET",
        "to_domain_name" => "POT",
        "price" => 123,
        "keys" => array()
    ),"token", null)["token"];

