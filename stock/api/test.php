<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/test.php";
include_once "scheme.php";

$usdtOwner = "123";
$solOwner = "321";
$dogeOwner = "141";

requestEquals("localhost/stock/api/create_coin.php",
    array(
        "token" => $usdtOwner,
        "ticker" => "USDT",
        "logo" => "http://localhost/stock/img/coin/USDT.svg",
        "name" => "Tether",
        "description" => "Good usd coin",
        "supply" => "10000",
        "price" => "1",
        "starter_supply" => "10",
    ), "result", true);

requestEquals("localhost/stock/api/create_coin.php",
    array(
        "token" => $solOwner,
        "ticker" => "SOL",
        "logo" => "http://localhost/stock/img/coin/SOL.svg",
        "name" => "Solana",
        "description" => "Good coin",
        "supply" => "2000",
        "price" => "1000",
        "starter_supply" => "1",
    ), "result", true);


requestEquals("localhost/stock/api/place.php",
    array("token" => $usdtOwner, "ticker" => "SOL", "is_sell" => "0", "price" => 1000, "amount" => 1), "result", true);


requestEquals("localhost/stock/api/place.php",
    array("token" => $usdtOwner, "ticker" => "SOL", "is_sell" => "0", "price" => 10, "amount" => 1), "result", true);
requestEquals("localhost/stock/api/place.php",
    array("token" => $usdtOwner, "ticker" => "SOL", "is_sell" => "0", "price" => 5, "amount" => 1), "result", true);
requestEquals("localhost/stock/api/place.php",
    array("token" => $usdtOwner, "ticker" => "SOL", "is_sell" => "0", "price" => 1, "amount" => 1), "result", true);
requestEquals("localhost/stock/api/place.php",
    array("token" => $solOwner, "ticker" => "SOL", "is_sell" => "1", "price" => 5, "amount" => 1), "result", true);
requestEquals("localhost/stock/api/place.php",
    array("token" => $solOwner, "ticker" => "SOL", "is_sell" => "1", "price" => 5, "amount" => 1), "result", true);

// ieo fail
requestEquals("localhost/stock/api/create_coin.php",
    array(
        "token" => $usdtOwner,
        "ticker" => "GFC",
        "logo" => "http://localhost/stock/img/coin/DOGE.svg",
        "name" => "Gas Free Coin",
        "description" => "Good coin for doge",
        "supply" => "10000",
        "price" => "0.2",
        "starter_supply" => "100",
    ), "result", true);

requestEquals("localhost/stock/api/place.php",
    array("token" => $usdtOwner, "ticker" => "GFC", "is_sell" => "0", "price" => 0.2, "amount" => 100), "result", true);
requestEquals("localhost/stock/api/drop_start.php",
    array(token => $usdtOwner, ticker => GFC, type => SIMPLE, total => 5000, reward => 10), result, true);


requestEquals("localhost/stock/api/create_coin.php",
    array(
        "token" => $dogeOwner,
        "ticker" => "DOGE",
        "logo" => "http://localhost/stock/img/coin/DOGE.svg",
        "name" => "Dogecoin",
        "description" => "Coin for dogs",
        "supply" => "2000",
        "price" => "1",
        "starter_supply" => "1",
    ), "result", true);


requestEquals("localhost/stock/api/place.php",
    array("token" => $solOwner, "ticker" => "SOL", "is_sell" => "0", "price" => 1, "amount" => 1), "result", true);


/*requestEquals("localhost/stock/api/email_send_code.php",
    array(token => $usdtOwner, email => "x29a100@gmail.com"), result, true);*/

//requestEquals("localhost/stock/api/bot_spred.php", array(), "result", true);







