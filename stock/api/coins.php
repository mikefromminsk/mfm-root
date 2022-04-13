<?php
include_once "utils.php";

$response[coins] = array_to_map(selectWhere("coins", []), ticker);

foreach ($response[coins] as &$item) {
    $item[price] = doubleval($item[price]);
    $item[change24] = doubleval($item[change24]);
}

echo json_encode($response);