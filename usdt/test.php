<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/db/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt/utils.php";

http_post_json("localhost/data/test/test.php", []);

$user1 = "user1";

$gas_index = 15;
function gas()
{
    return [
        gas_address => admin,
        gas_password => password . $GLOBALS["gas_index"],
        gas_next_hash => md5(password . (++$GLOBALS["gas_index"])),
    ];
}

assertEquals("reg", http_post_json("localhost/data/create.php",
    [path => "usdt/wallet", address => admin, next_hash => md5(password), amount => 10000] + gas())[wallet][amount],
    10000);

assertEquals("reg", http_post_json("localhost/usdt/reg/reg.php",
    [address => $user1, next_hash => md5(password)])[success],
    true);

assertNotEquals("deposit_start",
    $deposit_address = http_post_json("localhost/usdt/deposit/start.php",
        [receiver => $user1])[deposit_address],
    null);

assertEquals("usdt delegate", http_post_json("localhost/usdt/delegate.php", [
        address => USDT_OWNER,
        password => password,
        script => "usdt/deposit/check",
    ] + gas())[success], true);

assertEquals("deposit_check",
    http_post_json("localhost/usdt/deposit/check.php",
        [deposit_address => $deposit_address])[deposited],
    5);

assertEquals("balance usdt",
    http_post_json("localhost/data/balance/balance.php",
        [address => $user1, wallet_path => "usdt/wallet"]),
    5);

$user2 = "user2";

assertEquals("reg", http_post_json("localhost/usdt/reg/reg.php",
    [address => $user2, next_hash => md5(password)])[success],
    true);

assertEquals("send usdt", http_post_json("localhost/usdt/send/send.php", [
    from_address => $user1,
    to_address => $user2,
    password => password,
    next_hash => md5(password2),
    amount => 4,
] + gas())[sent], 4);
