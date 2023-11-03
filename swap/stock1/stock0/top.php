<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock0/utils.php";

description(basename(__FILE__));

$top = [
    "HRP_USDT",
];

$response["top"] = [];
foreach ($top as $pair) {
    $pair_data["pair"] = $pair;
    $pair_data["rate"] = dataGet(["pairs", $pair, "rate"], $pass);
    $response["top"][] = $pair_data;
}

echo json_encode($response);