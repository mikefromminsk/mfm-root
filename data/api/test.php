<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/schema.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/utils.php";

dataSet(["login", "test1"], "123");
assertEquals("data put", dataGet(["login", "test1"]), "123");

dataSet(["login", "test2"], "123");
assertEquals("data put", dataGet(["login", "test2"]), "123");