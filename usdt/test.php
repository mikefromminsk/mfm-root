<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt/utils.php";

dataWalletInit([usdt, wallet], USDT_OWNER, 1000.0);

$receiver = "x29a100@gmail.com";
$receiver2 = "root@gmail.com";

assertEquals("reg",
    http_post_json("localhost/usdt/reg.php", [address => $receiver, next_hash => md5(password)])[result],
    true);

assertNotEquals("deposit_start",
    $deposit_address = http_post_json("localhost/usdt/deposit_start.php", [receiver => $receiver])[deposit_address],
    null);

assertEquals("deposit_check",
    http_post_json("localhost/usdt/deposit_check.php", [deposit_address => $deposit_address])[deposited],
    100);

assertEquals("reg",
    http_post_json("localhost/usdt/reg.php", [address => $receiver2, next_hash => md5(password)])[result],
    true);

assertEquals("send",
    http_post_json("localhost/usdt/send.php",
        [
            fromAddress => $receiver,
            toAddress => $receiver2,
            password => password,
            next_hash => md5(password2),
            amount => 89,
        ])[sended],
    89);
