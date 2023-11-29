<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/utils.php";

$path = "data/wallet";
$address = "admin";
$deadline = time() + 1000;

assertEquals("reg",
    http_post("localhost/drop/start.php", [
        path => $path,
        address => $address,
        deadline => $deadline,
        reward => $deadline,
    ])[result],
    true);
