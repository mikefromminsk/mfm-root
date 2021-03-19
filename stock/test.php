<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/test.php";

$user1_token = requestNotNull("localhost/stock/reg/reg.php",
    array("login" => "user1", "password" => "123",), "token")["token"];
requestEquals("localhost/stock/new_coin/new_coin.php",
    array("token" => $user1_token, "domain_name" => "HRP", "postfix_length" => 2), "added", 100);


$user2_token = requestNotNull("localhost/stock/reg/reg.php",
    array("login" => "user2", "password" => "123",), "token")["token"];
requestEquals("localhost/stock/new_coin/new_coin.php",
    array("token" => $user2_token, "domain_name" => "USDT", "postfix_length" => 2), "added", 100);



requestEquals("localhost/stock/pairs/trade/new_limit_request.php",
    array("token" => $user1_token, "pair" => "HTP_USDT", "give" => "HRP", "want" => "USDT", "give_count" => 1, "want_count" => 5), "push_request", true);
requestEquals("localhost/stock/pairs/trade/new_limit_request.php",
    array("token" => $user1_token, "pair" => "HTP_USDT", "give" => "HRP", "want" => "USDT", "give_count" => 1, "want_count" => 4), "push_request", true);
requestEquals("localhost/stock/pairs/trade/new_limit_request.php",
    array("token" => $user1_token, "pair" => "HTP_USDT", "give" => "HRP", "want" => "USDT", "give_count" => 1, "want_count" => 3), "push_request", true);
requestEquals("localhost/stock/pairs/trade/new_limit_request.php",
    array("token" => $user2_token, "pair" => "HTP_USDT", "give" => "USDT", "want" => "HRP", "give_count" => 2, "want_count" => 1), "push_request", true);
requestEquals("localhost/stock/pairs/trade/new_limit_request.php",
    array("token" => $user2_token, "pair" => "HTP_USDT", "give" => "USDT", "want" => "HRP", "give_count" => 1, "want_count" => 1), "push_request", true);
requestEquals("localhost/stock/pairs/trade/new_limit_request.php",
    array("token" => $user2_token, "pair" => "HTP_USDT", "give" => "USDT", "want" => "HRP", "give_count" => 0.5, "want_count" => 1), "push_request", true);



/*
requestEquals("localhost/stock/api/account.php",
    array("token" => $user1_token), "account.USDT", 1500);
requestEquals("localhost/stock/api/account.php",
    array("token" => $user1_token), "account.HRP", 2);
requestEquals("localhost/stock/api/account.php",
    array("token" => $user2_token), "account.USDT", 1500);
requestEquals("localhost/stock/api/account.php",
    array("token" => $user2_token), "account.HRP", 0);


requestEquals("localhost/stock/api/income.php",
    array("login" => "user1", "domain_name" => "USDT", "count" => 3000), "added", 3000);
requestEquals("localhost/stock/api/income.php",
    array("login" => "user2", "domain_name" => "HRP", "count" => 6), "added", 6);

requestEquals("localhost/stock/api/account.php",
    array("token" => $user1_token), "account.USDT", 4500);
requestEquals("localhost/stock/api/account.php",
    array("token" => $user2_token), "account.HRP", 6);



requestEquals("localhost/stock/api/limit.php",
    array("token" => $user2_token, "give" => "HRP", "want" => "USDT", "give_count" => 1, "want_count" => 1000), "push_request", true);
requestEquals("localhost/stock/api/limit.php",
    array("token" => $user2_token, "give" => "HRP", "want" => "USDT", "give_count" => 3, "want_count" => 1000), "push_request", true);
requestEquals("localhost/stock/api/limit.php",
    array("token" => $user2_token, "give" => "HRP", "want" => "USDT", "give_count" => 2, "want_count" => 1000), "push_request", true);
requestEquals("localhost/stock/api/limit.php",
    array("token" => $user1_token, "give" => "USDT", "want" => "HRP", "give_count" => 1000, "want_count" => 1), "satisfied", 1000);

requestEquals("localhost/stock/api/account.php",
    array("token" => $user1_token), "account.USDT", 2500);
requestEquals("localhost/stock/api/account.php",
    array("token" => $user2_token), "account.HRP", 3);

requestEquals("localhost/stock/api/requests.php",
    array("give" => "USDT", "want" => "HRP"), "satisfied", 1000);*/

//echo json_encode($result);

/*requestEquals("localhost/stock/api/requests.php",
    array(
        "give" => "HRP",
        "want" => "USDT"
    ), "limit", true);



$hrp_received = requestNotEquals("localhost/stock/api/output.php",
    array(
        "token" => $user1_token,
        "domain_name" => "HRP",
        "output_count" => 1000,
    ), "keys", null);


requestEquals("localhost/balance/api/save.php",
    array(
        "token" => $user1_token,
        "domain_name" => "HRP",
        "keys" => $hrp_received,
    ), "added", 1000);*/


