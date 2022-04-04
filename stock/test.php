<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/scheme.php";

$token1 = "123";
$token2 = "321";
requestEquals("localhost/stock/create_currency.php",
    array("token" => $token1, "ticker" => "coin"), "result", true);
requestEquals("localhost/stock/create_currency.php",
    array("token" => $token2, "ticker" => "usdt"), "result", true);


requestEquals("localhost/stock/place.php",
    array("token" => $token2, "ticker" => "coin", "is_sell" => "0", "price" => 10, "amount" => 1), "result", true);
requestEquals("localhost/stock/place.php",
    array("token" => $token2, "ticker" => "coin", "is_sell" => "0", "price" => 5, "amount" => 1), "result", true);
requestEquals("localhost/stock/place.php",
    array("token" => $token2, "ticker" => "coin", "is_sell" => "0", "price" => 1, "amount" => 1), "result", true);

requestEquals("localhost/stock/place.php",
    array("token" => $token1, "ticker" => "coin", "is_sell" => "1", "price" => 5, "amount" => 1), "result", true);




