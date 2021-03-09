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
        "domain_name" => "PAIN",
        "keys" => $pain["keys"],
    ),"added", 10);

/*requestEquals("localhost/dark_stock/limit.php",
    array(
        "token" => $user1_token,
        "from" => "HRP",
        "to" => "PAIN",
        "price" => 50000,
        "count" => 2
    ),"limit", true);*/

$result = requestEquals("localhost/dark_stock/limit.php",
    array(
        "token" => $user1_token,
        "from" => "PAIN",
        "to" => "HRP",
        "price" => 0.0001,
        "count" => 10
    ),"limit", true);

/*requestEquals("localhost/dark_stock/limit.php",
    array(
        "token" => $user1_token,
        "from" => "PAIN",
        "to" => "HRP",
        "price" => 0.0002,
        "count" => 4
    ),"limit", true);


$result = requestEquals("localhost/dark_stock/requests.php",
    array(
        "from" => "PAIN",
        "to" => "HRP",
    ),"from.123.user1", 2);

*/
echo json_encode($result);

//oposite request
//rates
//oposite request
//requests
//volume
//deals
