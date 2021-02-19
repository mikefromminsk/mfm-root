<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";

$payment_id = get_required("payment_id");

$response["payment"] = selectRowWhere("payments", array("payment_id" => $payment_id));

echo json_encode($response);