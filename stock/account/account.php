<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/auth.php";

description(basename(__FILE__));

$response["login"] = $login;
$response["account"] = dataGet(["users", $login], $pass);

foreach ($response["balance"] as $domain_name => $count) {
    $response["coins"][] = dataGet(["coins", $domain_name], $pass);
}

echo json_encode_readable($response);