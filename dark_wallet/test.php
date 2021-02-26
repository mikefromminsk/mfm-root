<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/test.php";


$admin_token = http_post("localhost/dark_wallet/reg.php", array(
    "login" => "admin",
    "password" => "123",
))["token"];

$keys = http_post("localhost/dark_wallet/coin_generate.php", array(
    "token" => $admin_token,
    "domain_name" => "POT",
    "domain_postfix_length" => "2",
))["keys"];
$keys = http_post("localhost/dark_wallet/income.php", array(
    "token" => $admin_token,
    "keys" => $keys,
));

foreach ($friends as $friend) {
    http_post("$friend/dark_wallet/hosting.php", array(
        "domain_name" => "POT",
        "domain_postfix_length" => "2",
    ));
}





$user1_token = http_post("localhost/dark_wallet/reg.php", array(
    "login" => "user1",
    "password" => "123",
))["token"];


// buy pots payment_start.php;
// save pots payment_finish.php;

$response = http_post("localhost/dark_wallet/send.php", array(
    "token" => $admin_token,
    "receiver" => "user1",
    "domain_name" => "POT",
    "count" => 10,
));
$coins = http_post("localhost/dark_wallet/coins.php", array(
    "token" => $user1_token,
));
$user1_pot_keys = $coins["income"]["admin"]["order123"]["keys"];
$coins = http_post("localhost/dark_wallet/income.php", array(
    "token" => $user1_token,
    "keys" => $user1_pot_keys,
));




$keys = http_post("localhost/dark_wallet/coin_generate.php", array(
    "domain_name" => "TET",
    "domain_postfix_length" => "2",
    "keys" => array_slice($user1_pot_keys, 0, 2),
))["keys"];
http_post("localhost/dark_wallet/wallet/income.php", array(
    "token" => $user1_token,
    "keys" => $keys,
));


$payment_keys = [
    array_slice($user1_pot_keys, 2, 2),
    array_slice($user1_pot_keys, 4, 2),
    array_slice($user1_pot_keys, 6, 2),
];
for($i = 0; $i < 3; $i++) {
    $friend = $friends[$i];
    $payment = $payment_keys[$i];
    $response = http_post("$friend/dark_wallet/hosting.php", array(
        "domain_name" => "TET",
        "domain_postfix_length" => "2",
        "keys" => $payment,
    ));
}



$user2_token = http_post("localhost/dark_wallet/reg.php", array(
    "login" => "user2",
    "password" => "123",
))["token"];

$response = http_post("localhost/dark_wallet/send.php", array(
    "token" => $user1_token,
    "receiver" => "user2",
    "domain_name" => "TET",
    "count" => 10,
));
$coins = http_post("localhost/dark_wallet/coins.php", array(
    "token" => $user1_token,
));
$coins = http_post("localhost/dark_wallet/income.php", array(
    "token" => $user1_token,
    "keys" => $coins["income"]["user1"]["transaction123"]["keys"],
));



