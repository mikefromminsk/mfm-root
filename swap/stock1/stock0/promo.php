<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock0/utils.php";

$promo_code = get_required("code");

description(basename(__FILE__));

$response["promo"] = dataGet(["promos", $promo_code], $pass);

echo json_encode($response);