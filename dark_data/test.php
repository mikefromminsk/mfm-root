<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/init.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/utils.php";


/*$data_id = dataPut("login.test1.domain.pot", "test2", "pass", "123");
$data_id = dataPut("login.test1.domain.pot", "test3", "pass", "321");
$value = dataGet("login", "test1",  null);
assertNotEquals("data get", $value["test2"], "123");
assertNotEquals("data get", $value["test3"], "321");*/

/*
$data_id = dataPut("login.test1", "test2", "pass", "123");
$value = dataGet("login.test1", "test3",  "pass");
assertEquals("data put", $value, "123");

$data_id = dataPut("login.test1", "test3", "pass", "123");
$value = dataGet("login.test1", "test3",  "pass");
assertEquals("data put", $value, "123");*/


/*$data_id = dataPush("login.test1", "array", "pass", "321");
$value = dataGet("login.test1.array", "0",  "pass");
assertEquals("data push", $value, "321");*/




