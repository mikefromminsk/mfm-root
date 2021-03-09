<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/init.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/utils.php";




$data_id = dataSet(["login", "test1"], "pass", "123");
$value = dataGet(["login", "test1"], "pass");
assertEquals("data put", $value, "123");

$data_id = dataSet(["login", "test2"], "pass", "123");
$value = dataGet(["login", "test2"], "pass");
assertEquals("data put", $value, "123");


$data_id = dataAdd(["login", "test2", "array"], "pass", "321");
$value = dataGet(["login", "test2", "array", "0"], "pass");
assertEquals("data push", $value, "321");


$data_id = dataSet(["login", "hrp", "test2"], "pass", "123");
$data_id = dataSet(["login", "hrp", "test3"], "pass", "321");
$value = dataGet(["login", "hrp"], "pass");
assertNotEquals("data get", $value["test2"], "123");
assertNotEquals("data get", $value["test3"], "321");
