<?php

include_once "yandex_utils.php";

$response = yandex("account-info");

$result["balance"] = $response["balance"];

echo json_encode($result);