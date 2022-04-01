<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock0/auth.php";

$first = get_required("first");
$second = get_required("second");

description(basename(__FILE__));

if ($first == $second) error("domains are equal");


$first_coin = dataGet(["coins", $first], $pass);
$second_coin = dataGet(["coins", $second], $pass);

if ($first_coin["owner"] != $login && $second_coin["owner"] != $login) error("you are not owner of all coins");

$pair = $first . "_" . $second;

$response["success"] = dataSet(["pairs", $pair, "rate"], $pass, 0) ? true : false;

if ($response["success"] == false) error("create pair error");

echo json_encode($response);