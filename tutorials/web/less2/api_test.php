<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

$number = get_int_required("number");

$response["result"] = $number;

echo json_encode($response);