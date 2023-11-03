<?php
include_once "utils.php";

$response[coins] = array_to_map(selectWhere(coins, [type => ACTIVE]), ticker);

echo json_encode($response);