<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/domains/api/utils.php";


$pain = domains_generate_keys("PAIN", 1);

dataSet(["store"], $admin_token, $pain);

$store_count = dataCount(["store"], $admin_token);

if ($store_count != 10)
    error("store is empty");

