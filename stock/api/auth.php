<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/api/utils.php";

$token = get_required("token");

$user_id = selectRowWhere("users", [token => $token])["user_id"];

if ($user_id == null) {
    $user_id = insertRowAndGetId("users", [token => $token]);
    insertRow("balances", [user_id => $user_id, ticker => "USDT", spot => 0, blocked => 0]);
}
