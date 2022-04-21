<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/test.php";
include_once "scheme.php";

$token1 = "123";
$token2 = "321";

requestEquals("localhost/stock/api/create_coin.php",
    array(
        "token" => $token1,
        "ticker" => "USDT",
        "name" => "Tether",
        "description" => "Good usd coin",
        "supply" => "100000",
        "price" => "1",
        "starter_supply" => "10",
    ), "result", true);

requestEquals("localhost/stock/api/create_coin.php",
    array(
        "token" => $token2,
        "ticker" => "SOL",
        "name" => "Solana",
        "description" => "Good coin",
        "supply" => "10000",
        "price" => "1000",
        "starter_supply" => "1",
    ), "result", true);


requestEquals("localhost/stock/api/place.php",
    array("token" => $token1, "ticker" => "SOL", "is_sell" => "0", "price" => 1000, "amount" => 1), "result", true);


requestEquals("localhost/stock/api/place.php",
    array("token" => $token1, "ticker" => "SOL", "is_sell" => "0", "price" => 10, "amount" => 1), "result", true);
requestEquals("localhost/stock/api/place.php",
    array("token" => $token1, "ticker" => "SOL", "is_sell" => "0", "price" => 5, "amount" => 1), "result", true);
requestEquals("localhost/stock/api/place.php",
    array("token" => $token1, "ticker" => "SOL", "is_sell" => "0", "price" => 1, "amount" => 1), "result", true);
requestEquals("localhost/stock/api/place.php",
    array("token" => $token2, "ticker" => "SOL", "is_sell" => "1", "price" => 5, "amount" => 1), "result", true);
requestEquals("localhost/stock/api/place.php",
    array("token" => $token2, "ticker" => "SOL", "is_sell" => "1", "price" => 5, "amount" => 1), "result", true);


requestEquals("localhost/stock/api/create_coin.php",
    array(
        "token" => $token2,
        "ticker" => "DOGE",
        "name" => "Doge coin",
        "description" => "Good coin for doge",
        "supply" => "10000",
        "price" => "0.2",
        "starter_supply" => "100",
    ), "result", true);

requestEquals("localhost/stock/api/place.php",
    array("token" => $token1, "ticker" => "DOGE", "is_sell" => "0", "price" => 0.2, "amount" => 1), "result", true);

requestEquals("localhost/stock/api/ieo_close.php", array("ticker" => "DOGE"), "returned", 0.2);


//requestEquals("localhost/stock/api/bot_spred.php", array(), "result", true);





