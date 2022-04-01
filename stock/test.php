<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/scheme.php";

$token = "123";
requestEquals("localhost/stock/create_currency.php",
    array("tag" => "coin", "token" => $token), "result", true);
requestEquals("localhost/stock/create_currency.php",
    array("tag" => "usdt", "token" => $token), "result", true);

requestEquals("localhost/stock/create_currency.php",
    array("tag" => "coin", "token" => $token), "result", true);

