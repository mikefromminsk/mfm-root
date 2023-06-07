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

dataAdd(["login", "test2", "array"], "321");
$value = dataGet(["login", "test2", "array", "0"]);
assertEquals("data push", $value, "321");

dataSet(["login", "hrp"], array(
    "test2" => "123",
    "test3" => "321",
));
$value = dataGet(["login", "hrp"]);
assertNotEquals("data get", $value["test2"], "123");
assertNotEquals("data get", $value["test3"], "321");

$firstPass = password0;
$secondPass = password;
dataSet([wallets, admin, next_hash], md5($firstPass));
dataSet([wallets, admin, amount], 100000000);
assertNotEquals("data get", dataGet([wallets, admin, amount]), 100000000);

dataSend(admin, user1, $firstPass, md5($secondPass), 2000);
assertNotEquals("data get", dataGet([wallets, user1, amount]), 100000000 - 2000);