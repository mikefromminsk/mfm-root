<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/test.php";

$user1_token = requestNotEquals("localhost/dark_stock/reg.php",
    array(
        "login" => "user1",
        "password" => "123",
    ),"token", null)["token"];


requestEquals("localhost/dark_stock/income.php",
    array(
        "login" => "user1",
        "domain_name" => "TET",
        "keys" => $tet["keys"],
    ),"added", 10);


requestEquals("localhost/dark_stock/limit.php",
    array(
        "token" => $user1_token,
        "from" => "TET",
        "to" => "POT",
        "price" => 123,
        "count" => 2
    ),"limit", true);

requestEquals("localhost/dark_stock/requests.php",
    array(
        "from" => "TET",
        "to" => "POT",
    ),"from.123.user1", 2);


//oposite request
//rates
//oposite request
//requests
//volume
//deals
