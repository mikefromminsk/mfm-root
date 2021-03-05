<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/test.php";

$admin_token = requestNotEquals("localhost/dark_wallet/reg.php",
    array(
        "login" => "admin",
        "password" => "123",
    ), "token", null)["token"];


function encode_decode(&$keys)
{
    foreach ($keys as $key => $value)
        $keys[$key] = strrev($value);
}

encode_decode($keys);

$keys = $pot["keys"];

$result = requestEquals("localhost/dark_wallet/save.php",
    array(
        "token" => $admin_token,
        "domain_name" => "POT",
        "keys" => $keys,
    ), "added", 10);


// buy pots tariffs.php;
// buy pots payment_start.php;
// save pots payment_finish.php;

$user1_token = requestNotEquals("localhost/dark_wallet/reg.php",
    array(
        "login" => "user1",
        "password" => "123",
    ), "token", null)["token"];

encode_decode($keys);
$pot_send_keys = array_slice($keys, 0, 10);

requestEquals("localhost/dark_wallet/send.php",
    array(
        "token" => $admin_token,
        "receiver" => "user1",
        "domain_name" => "POT",
        "keys" => $pot_send_keys,
    ), "added", 10);


$pot_received = requestCount("localhost/dark_wallet/account.php",
    array(
        "token" => $user1_token,
    ), "income.admin.POT", 10)["income"]["admin"]["POT"];

encode_decode($pot_received);

requestEquals("localhost/dark_wallet/save.php",
    array(
        "token" => $user1_token,
        "domain_name" => "POT",
        "keys" => $pot_received,
    ), "added", 10);


/*

$payment_keys = [
    array_slice($user1_pot_keys, 2, 2),
    array_slice($user1_pot_keys, 4, 2),
    array_slice($user1_pot_keys, 6, 2),
];
for($i = 0; $i < 3; $i++) {
    $friend = $friends[$i];
    $payment = $payment_keys[$i];
    $response =http_post_json("$friend/dark_wallet/hosting.php", array(
        "domain_name" => "TET",
        "domain_postfix_length" => "2",
        "keys" => $payment,
    ));
}

*/


