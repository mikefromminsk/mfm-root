<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/auth.php";

$search = get_required("search");

description(basename(__FILE__));

$pairs = dataLike(["pairs"], $pass, "%$search%", true, 0, 10);

$response["found"] = [];
foreach ($pairs as $pair)
    $response["found"][] = dataGet(["pairs", $pair], $pass);

echo json_encode($response);