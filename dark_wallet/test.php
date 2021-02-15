<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/utils.php";

// reg admin
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/init.php";

// generate pot coin_generate.php login admin
$response = http_get("localhost/dark_wallet/coin_generate.php?domain_name=POT&domain_postfix_length=0");

$response = http_post("localhost/dark_wallet/coin_generate.php", array(
    "domain_name" => "TET",
    "domain_postfix_length" => "0",
    "payment_keys" => $response["keys"],
));

echo ($response);

// reg user reg.php
//http_get("localhost/dark_wallet/reg.php?login=user&password_token=" . hash("sha256", 123));

// buy pots payment_start.php

// save pots payment_finish.php


// pay pot to friends coin_generate.php

// generate tet local  coin_generate.php
// send tet to fiends  coin_generate.php

// reg receiver_user remote  reg.php
// user validate and download 10 tet local wallet_download.php
// remote_user upload 10 tet remote wallet_upload.php


