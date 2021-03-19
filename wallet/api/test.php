<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/domains/api/test.php";

$admin_token = requestNotNull("localhost/wallet/api/reg.php",
    array(
        "login" => $login,
        "password" => "123",
    ), "token")["token"];



function encode_decode(&$keys)
{
    foreach ($keys as $key => $value)
        $keys[$key] = strrev($value);
}

encode_decode($hrp);

$result = requestEquals("localhost/wallet/api/save.php",
    array(
        "token" => $admin_token,
        "domain_name" => "HRP",
        "keys" => $hrp,
    ), "added", 10);


// buy hrps tariffs.php;
// buy hrps payment_start.php;
// save hrps payment_finish.php;

$user1_token = requestNotNull("localhost/wallet/api/reg.php",
    array(
        "login" => "user1",
        "password" => "123",
    ), "token")["token"];

encode_decode($hrp);
$hrp_send_keys = array_slice($hrp, 0, 10);

requestEquals("localhost/wallet/api/send.php",
    array(
        "token" => $admin_token,
        "receiver" => "user1",
        "domain_name" => "HRP",
        "keys" => $hrp_send_keys,
    ), "added", 10);


$hrp_received = requestCount("localhost/wallet/api/account.php",
    array(
        "token" => $user1_token,
    ), "income.admin.HRP", 10)["income"][$login]["HRP"];

encode_decode($hrp_received);

requestEquals("localhost/wallet/api/save.php",
    array(
        "token" => $user1_token,
        "domain_name" => "HRP",
        "keys" => $hrp_received,
    ), "added", 10);


/*

$payment_keys = [
    array_slice($user1_hrp_keys, 2, 2),
    array_slice($user1_hrp_keys, 4, 2),
    array_slice($user1_hrp_keys, 6, 2),
];
for($i = 0; $i < 3; $i++) {
    $friend = $friends[$i];
    $payment = $payment_keys[$i];
    $response =http_post_json("$friend/wallet/hosting.php", array(
        "domain_name" => "PAIN",
        "domain_postfix_length" => "2",
        "keys" => $payment,
    ));
}

*/


