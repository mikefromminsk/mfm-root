<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/test.php";

$admin_token = requestNotEquals("localhost/dark_wallet/reg.php",
    array(
        "login" => "admin",
        "password" => "123",
    ), "token", null)["token"];

$keys = requestCount("localhost/dark_wallet/hosting.php",
    array(
        "token" => $admin_token,
        "domain_name" => "POT",
        "domain_postfix_length" => "2",
        "keys" => array(),
    ), "added", 100);


function generate_domains($domain_name, $domain_postfix_length)
{
    $keys = array();
    $domains = array();
    for ($i = 0; $i < pow(10, $domain_postfix_length); $i++) {
        $new_domain = $domain_name . sprintf("%0" . $domain_postfix_length . "d", $i);
        $keys[$new_domain] = random_id();
        $domains[] = array(
            "domain_name" => $new_domain,
            "domain_prev_key" => null,
            "domain_key_hash" => hash_sha56($keys[$new_domain]),
            "server_repo_hash" => null,
        );
    }
    return array("keys" => $keys, "domains" => $domains);
}

$domains = generate_domains("POT", 2);


requestCount("localhost/dark_domain/domains.php",
    array(
        "domains" => $domains["domains"],
    ), "domains", 100);


$keys = $domains["keys"];

function encode_decode(&$keys)
{
    foreach ($keys as $key => $value)
        $keys[$key] = strrev($value);
}

encode_decode($keys);

$result = requestEquals("localhost/dark_wallet/save.php",
    array(
        "token" => $admin_token,
        "domain_name" => "POT",
        "keys" => $keys,
    ), "added", 100);

/*

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/properties.php";

foreach ($servers as $server) {
    http_post_json("$server/dark_wallet/hosting.php", array(
        "domain_name" => "POT",
        "domain_postfix_length" => "2",
    ));
}
*/


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

$keys =http_post_json("localhost/dark_wallet/coin_generate.php", array(
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
    $response =http_post_json("$friend/dark_wallet/hosting.php", array(
        "domain_name" => "TET",
        "domain_postfix_length" => "2",
        "keys" => $payment,
    ));
}



$user2_token =http_post_json("localhost/dark_wallet/reg.php", array(
    "login" => "user2",
    "password" => "123",
))["token"];

$response =http_post_json("localhost/dark_wallet/send.php", array(
    "token" => $user1_token,
    "receiver" => "user2",
    "domain_name" => "TET",
    "count" => 10,
));
$coins =http_post_json("localhost/dark_wallet/coins.php", array(
    "token" => $user1_token,
));
$coins =http_post_json("localhost/dark_wallet/income.php", array(
    "token" => $user1_token,
    "keys" => $coins["income"]["user1"]["transaction123"]["keys"],
));
*/


