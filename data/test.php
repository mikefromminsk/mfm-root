<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/init.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

dataSet(["login", "test1"], "123");
$value = dataGet(["login", "test1"]);
assertEquals("data put", $value, "123");

dataSet(["login", "test2"], "123");
$value = dataGet(["login", "test2"]);
assertEquals("data put", $value, "123");

/*dataAdd(["login", "test2", "array"], "321");
$value = dataGet(["login", "test2", "array", "0"]);
assertEquals("data push", $value, "321");

dataSet(["login", "hrp"], array(
    "test2" => "123",
    "test3" => "321",
));
$value = dataGet(["login", "hrp"]);
assertNotEquals("data get", $value["test2"], "123");
assertNotEquals("data get", $value["test3"], "321");*/

dataWalletInit([data, wallet], admin, md5(password), 100000000.0);
assertEquals("dataWalletInit", dataGet([data, wallet, admin, amount]), 100000000.0);

dataWalletReg([data, wallet], user1, md5(password));
dataWalletSend([data, wallet], admin, user1, 2000.0, password, md5(password2));
dataWalletSend([data, wallet], user1, admin, 1.0, password, md5(password2));
assertEquals("dataSend", dataGet([data, wallet, admin, amount]), 100000000.0 - 2000.0 + 1.0);

dataWalletDelegate([data, wallet], user1, password2, "data/testDelegate");

$_POST["gas_address"] = admin;
$_POST["gas_password"] = password2;
$_POST["gas_next_hash"] = md5(password3);
commit("commit");

assertEquals("testDelegate", http_post_json("localhost/data/testDelegate.php", [])[success], true);
assertEquals("balanceAfterBurn", strval(dataGet([data, wallet, user1, amount])), strval(1999 - FILE_ROW_SIZE));

$gas_index = 3;
function gas(){
    return [
        gas_address => admin,
        gas_password => password . $GLOBALS["gas_index"],
        gas_next_hash => md5(password . (++$GLOBALS["gas_index"])),
    ];
}

assertEquals("testReg", http_post_json("localhost/data/reg.php", [
    address => usdt_reg,
    next_hash => md5(password1),
] + gas())[success], true);

assertEquals("testSend", http_post_json("localhost/data/send.php", [
    fromAddress => admin,
    toAddress => usdt_reg,
    password => password . $gas_index,
    next_hash => md5(password . ++$gas_index),
    amount => 10000,
] + gas())[success], true);

assertEquals("testDelegate", http_post_json("localhost/data/delegate.php", [
    address => usdt_reg,
    password => password1,
    script => "usdt/reg/reg",
] + gas())[success], true);