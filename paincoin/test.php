<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/paincoin/init.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/test.php";

$email = "x29a100@mail.ru";
$request = requestNotNull("localhost/paincoin/pain.php",
    array("text" => "I am bad person", "email" => $email), "request")["request"];


$approve = requestEquals("localhost/paincoin/approve.php",
    array("request_id" => $request["request_id"],), "promo_count", 5);

//assertEquals("mail send", $response["mail_sent"], true);
assertNotEquals("promo_url", $approve["promo_url"], null);

requestCount($approve["promo_url"],
    array(), "keys", 5);

$user_token = requestEquals("localhost/dark_wallet/reg.php",
    array("login" => $email, "password"=> "123", "promo_url" => $approve["promo_url"]), "promo_added", 5)["token"];

