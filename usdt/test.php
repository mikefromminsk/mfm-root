<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/db/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt/utils.php";

http_post_json("localhost/data/test/test.php", []);

dataWalletInit([usdt, wallet], USDT_OWNER, md5(password), 1000000.0);

$receiver = "x29a100@gmail.com";

assertEquals("reg",
    $deposit_address = http_post_json("localhost/usdt/deposit/reg/reg.php",
        [address => $receiver, next_hash => md5(password)]),
    true);

assertNotEquals("deposit_start",
    $deposit_address = http_post_json("localhost/usdt/deposit/start.php", [receiver => $receiver])[deposit_address],
    null);

assertEquals("deposit_check",
    http_post_json("localhost/usdt/deposit/check.php", [deposit_address => $deposit_address])[deposited],
    100);

assertEquals("balance",
    http_post_json("localhost/usdt/balance/balance.php", [address => $receiver])[amount],
    100);

/*assertEquals("send",
    http_post_json("localhost/usdt/send.php",
        [
            fromAddress => $receiver,
            toAddress => $receiver2,
            password => password,
            next_hash => md5(password2),
            amount => 89,
        ])[sended],
    89);*/
