<?php
include_once "utils.php";

$response[coins] = array_to_map(selectWhere(coins, [type => COIN]), ticker);

echo json_encode($response);