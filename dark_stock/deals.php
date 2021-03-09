<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/utils.php";

$pair_name = get_required("pair_name");

description("deals");

$response["deals"] = dataGet(["deals", $pair_name], $admin_token, false, 0, 10);

echo json_encode($response);