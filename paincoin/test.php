<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/init.php";

$email = "x29a100@mail.ru";
$request_id = requestNotNull("localhost/paincoin/pain.php",
    array("text" => "I am bad person", "email" => $email), "request_id")["request_id"];

$promo_url = requestNotNull("localhost/paincoin/approve.php",
    array("request_id" => $request_id,), "promo_url")["promo_url"];


$user_token = requestNotNull("localhost/dark_wallet/reg.php",
    array("login" => $email, "password"=> "123", "promo_url" => $promo_url), "token")["token"];

