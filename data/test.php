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

/*dataSet(["login", "hrp"], array(
    "test2" => "123",
    "test3" => "321",
));
$value = dataGet(["login", "hrp"]);
assertNotEquals("data get", $value["test2"], "123");
assertNotEquals("data get", $value["test3"], "321");*/

dataWalletInit([data, wallet], admin, 100000000.0);
assertEquals("dataWalletInit", dataGet([data, wallet, admin, amount]), 100000000.0);

dataWalletReg([data, wallet], user1, md5(password));
dataSend([data, wallet], admin, user1, 2000.0);
dataSend([data, wallet], user1, admin, 1.0, password, md5(password2));
assertEquals("dataSend", dataGet([data, wallet, admin, amount]), 100000000.0 - 2000.0 + 1.0);
