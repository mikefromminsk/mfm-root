<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$path = get_required(path);
$address = get_required(address);
$deadline = get_required(deadline);
$reward = get_required(reward);

$path = explode("/", $path);

dataAdd([drop], [
    path => $path,
    address => $address,
    deadline => $deadline,
    reward => $reward,
]);

$response[result] = true;

echo json_encode($response);