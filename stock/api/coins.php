<?php
include_once "utils.php";

$response["result"] = array_to_map(selectWhere("coins", []), ticker);

echo json_encode($response);