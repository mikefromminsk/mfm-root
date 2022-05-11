<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/test.php";
include_once "scheme.php";

$usdtOwner = "123";
$solOwner = "321";

requestEquals("localhost/stock/api/create_coin.php",
    array(
        "token" => $usdtOwner,
        "ticker" => "USDT",
        "logo" => "http://localhost/stock/img/coin/USDT.svg",
        "name" => "Tether",
        "description" => "Good usd coin",
        "supply" => "100000",
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
        "supply" => "10000",
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
        "token" => $solOwner,
        "ticker" => "DOGE",
        "logo" => "http://localhost/stock/img/coin/DOGE.svg",
        "name" => "Doge coin",
        "description" => "Good coin for doge",
        "supply" => "10000",
        "price" => "0.2",
        "starter_supply" => "100",
    ), "result", true);

requestEquals("localhost/stock/api/place.php",
    array("token" => $usdtOwner, "ticker" => "DOGE", "is_sell" => "0", "price" => 0.2, "amount" => 1), "result", true);
requestEquals("localhost/stock/api/ieo_close.php",
    array("ticker" => "DOGE"), "returned", 0.2);


// tc
$tccOwner = "424";
$tccTrader1 = "4124";
$tccTrader2 = "41244";
requestEquals("localhost/stock/api/auth.php",
    array("token" => $tccTrader1, "email" => "tccTrader1"), "result", null);
requestEquals("localhost/stock/api/auth.php",
    array("token" => $tccTrader2, "email" => "tccTrader2"), "result", null);
requestEquals("localhost/stock/api/transfer.php",
    array("token" => $usdtOwner, "to_email" => "tccTrader1", "ticker" => "USDT", "amount" => 50), "result", true);
requestEquals("localhost/stock/api/transfer.php",
    array("token" => $usdtOwner, "to_email" => "tccTrader2", "ticker" => "USDT", "amount" => 50), "result", true);
requestEquals("localhost/stock/api/user.php",
    array("token" => $tccTrader1), "balances.USDT.spot", 50);
requestEquals("localhost/stock/api/user.php",
    array("token" => $tccTrader2), "balances.USDT.spot", 50);

requestEquals("localhost/stock/api/create_coin.php",
    array(
        "token" => $tccOwner,
        "email" => "tccEmail",
        "ticker" => "TCC",
        "logo" => "http://localhost/stock/img/coin/TCC.svg",
        "name" => "TC Coin",
        "description" => "Coin for trading competitions",
        "supply" => "10000",
        "price" => "1",
        "starter_supply" => "1",
    ), "result", true);
requestEquals("localhost/stock/api/place.php",
    array("token" => $tccTrader1, "ticker" => "TCC", "is_sell" => "0", "price" => 1, "amount" => 1), "result", true);


requestEquals("localhost/stock/api/place.php",
    array("token" => $tccOwner, "ticker" => "TCC", "is_sell" => "1", "price" => 1, "amount" => 100), "result", true);

$start = time() - 100;
requestEquals("localhost/stock/api/tc_start.php",
    array("token" => $tccOwner, "ticker" => "TCC", "start" => $start, "finish" => time() + 100, "reward" => 100), "result", true);

requestEquals("localhost/stock/api/place.php",
    array("token" => $tccTrader1, "ticker" => "TCC", "is_sell" => "0", "price" => 1, "amount" => 49), "result", true);

requestEquals("localhost/stock/api/place.php",
    array("token" => $tccTrader2, "ticker" => "TCC", "is_sell" => "0", "price" => 1, "amount" => 50), "result", true);

requestEquals("localhost/stock/api/tc_finish.php",
    array("ticker" => "TCC", "start" => $start), "result", true);

requestEquals("localhost/stock/api/user.php",
    array("token" => $tccTrader1), "balances.TCC.spot", 100);


requestEquals("localhost/stock/api/drop_start.php",
    array(token => $solOwner, ticker => SOL, type => SIMPLE, total => 100, reward => 10), result, true);

requestEquals("localhost/stock/api/email_send_code.php",
    array(token => $usdtOwner, email => "x29a100@gmail.com"), result, true);

//requestEquals("localhost/stock/api/bot_spred.php", array(), "result", true);







