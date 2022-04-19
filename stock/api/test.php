<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/test.php";
include_once "scheme.php";

$token1 = "123";
$token2 = "321";
requestEquals("localhost/stock/api/create_coin.php",
    array("token" => $token1, "ticker" => "SOL", "name" => "Solana"), "result", true);
requestEquals("localhost/stock/api/create_coin.php",
    array("token" => $token2, "ticker" => "USDT", "name" => "Tether"), "result", true);


requestEquals("localhost/stock/api/place.php",
    array("token" => $token2, "ticker" => "SOL", "is_sell" => "0", "price" => 1000, "amount" => 1), "result", true);
requestEquals("localhost/stock/api/place.php",
    array("token" => $token1, "ticker" => "SOL", "is_sell" => "1", "price" => 1000, "amount" => 1), "result", true);


requestEquals("localhost/stock/api/place.php",
    array("token" => $token2, "ticker" => "SOL", "is_sell" => "0", "price" => 10, "amount" => 1), "result", true);
requestEquals("localhost/stock/api/place.php",
    array("token" => $token2, "ticker" => "SOL", "is_sell" => "0", "price" => 5, "amount" => 1), "result", true);
requestEquals("localhost/stock/api/place.php",
    array("token" => $token2, "ticker" => "SOL", "is_sell" => "0", "price" => 1, "amount" => 1), "result", true);
requestEquals("localhost/stock/api/place.php",
    array("token" => $token1, "ticker" => "SOL", "is_sell" => "1", "price" => 5, "amount" => 1), "result", true);
requestEquals("localhost/stock/api/place.php",
    array("token" => $token1, "ticker" => "SOL", "is_sell" => "1", "price" => 5, "amount" => 1), "result", true);


requestEquals("localhost/stock/api/bot_spred.php", array(), "result", true);





