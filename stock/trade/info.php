<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/utils.php";

$pair = get_required("pair");
$first = explode("_", $pair)[0];
$second = explode("_", $pair)[1];

description(basename(__FILE__));

$response["sale"] = dataGet(["requests", $first, $second], $pass, true, 0, 10);
$response["buy"] = dataGet(["requests", $second, $first], $pass, false, 0, 10);

$response["rates"] = dataGet(["rates"], $pass);
$response["volume"] = dataGet(["volume"], $pass);

if (get("token") != null){
    include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/auth.php";
    $response["balance"][$first] = dataGet(["users", $login, "balance", $first], $pass) ?: 0;
    $response["balance"][$second] = dataGet(["users", $login, "balance", $second], $pass) ?: 0;
}

echo json_encode_readable($response);