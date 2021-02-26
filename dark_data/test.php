<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/init.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/utils.php";

$data_id = dataPut("login", "test", "pass", "123");

$value = dataSelect("login", "test",  "pass");

assertEquals("data put", $value, "123");