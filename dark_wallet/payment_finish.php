<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/utils.php";

$payment_id = get_required("payment_id");

updateWhere("payments", array("payment_time", time()), array("payment_id" => $payment_id));

data_put("users.$login.private.payments[]", $token, $payment_id);