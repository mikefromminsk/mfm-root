<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/paincoin/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";


$pain = domains_generate("PAIN", 1);

dataSet(["store"], $admin_token, $pain["keys"]);
