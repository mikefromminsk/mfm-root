<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/test.php";

$user1_token = requestNotEquals("localhost/dark_stock/reg.php",
    array(
        "login" => "user1",
        "password" => "123",
    ), "token", null)["token"];


requestEquals("localhost/dark_stock/income.php",
    array(
        "login" => "user1",
        "domain_name" => "PAIN",
        "keys" => $pain["keys"],
    ), "added", 10);




$result = requestEquals("localhost/dark_stock/limit.php",
    array("token" => $user1_token, "give" => "PAIN", "want" => "HRP", "give_count" => 1000, "want_count" => 1), "limit", true);
$result = requestEquals("localhost/dark_stock/limit.php",
    array("token" => $user1_token, "give" => "PAIN", "want" => "HRP", "give_count" => 1000, "want_count" => 3), "limit", true);
$result = requestEquals("localhost/dark_stock/limit.php",
    array("token" => $user1_token, "give" => "PAIN", "want" => "HRP", "give_count" => 1000, "want_count" => 2), "limit", true);
$result = requestEquals("localhost/dark_stock/limit.php",
    array("token" => $user1_token, "give" => "HRP", "want" => "PAIN", "give_count" => 1, "want_count" => 2000), "limit", true);

echo json_encode($result);


//oposite request
//rates
//oposite request
//requests
//volume
//deals
