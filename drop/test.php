<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$path = "data/wallet";
$address = "admin";
$deadline = time() + 1000;

assertEquals("reg",
    http_post_json("localhost/drop/start.php", [
        path => $path,
        address => $address,
        deadline => $deadline,
        reward => $deadline,
    ])[result],
    true);
