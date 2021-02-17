<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";

// reg admin
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/init.php";

// generate pot coin_generate.php login admin
/*$response = http_post("localhost/dark_wallet/coin_generate.php", array(
    "domain_name" => "POT",
    "domain_postfix_length" => "0",
));

$response = http_post("localhost/dark_wallet/coin_generate.php", array(
    "domain_name" => "TET",
    "domain_postfix_length" => "0",
    "payment_keys" => $response["keys"],
));


$response = http_post("localhost/dark_wallet/reg.php", array(
    "login" => "user",
    "password_token" => hash_sha56( 123),
));*/

/*$public1 = dh_public(3, 17, 22);
echo json_encode($public1);

$public2 = dh_public(3, 17, 2);
echo json_encode($public2);


$all_secret1 = dh_secret(17, 2, $public1);
echo json_encode($all_secret1);

$all_secret2 = dh_secret(17, 22, $public2);
echo json_encode($all_secret2);*/


// buy pots payment_start.php

// save pots payment_finish.php


// pay pot to friends coin_generate.php

// generate tet local  coin_generate.php
// send tet to fiends  coin_generate.php

// reg receiver_user remote  reg.php
// user validate and download 10 tet local wallet_download.php
// remote_user upload 10 tet remote wallet_upload.php


