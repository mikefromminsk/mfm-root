<?php

include_once "../../db-utils/db.php";
include_once "const.php";
include_once "messages_utils.php";

send(1, "Payment receive start", json_encode($_POST));
$sha1_hash = get_required("sha1_hash");

$notification_type = get_required("notification_type");
$operation_id = get_required("operation_id");
$amount = get_required("amount");
$currency = get_required("currency");
$datetime = get_required("datetime");
$sender = get_required("sender");
$codepro = get_required("codepro");
$label = get("label");
$notification_secret = $yandex_money_secret_code;

$test_string = "$notification_type&$operation_id&$amount&$currency&$datetime&$sender&$codepro&$notification_secret&$label";
$test_hash = hash("sha1", $test_string);

send(1, "Payment receive", $test_string);

if ($sha1_hash != $test_hash)
    error("receipt is invalid");

$lines = explode("&", $label);

$order_id = null;
$coin_code = null;
$coin_name = null;

foreach ($lines as $line) {
    $vals = explode("=", $line);
    if ($vals[0] == "coin_code")
        $coin_code = $vals[1];
    if ($vals[0] == "coin_name")
        $coin_name = $vals[1];
    if ($vals[0] == "order_id")
        $order_id = $vals[1];
}

if ($order_id != null && $coin_code != null && $coin_name != null) {

    $order_message = selectMap("select * from messages where message_type = '" . MESSAGE_ORDER_CREATE . "' and message_object_id = $order_id");
    if ($order_message != null) {
        $user_id = $order_message["user_id"];

        send($user_id, "Success paid", "Your order №$order_id has been paid. Your $coin_name are registering...", MESSAGE_ORDER_SUCCESS_PAID, $order_id);
        $update_success = update("update domain_keys set user_id = $user_id where user_id = 1 and coin_code = 'USD' limit $stock_fee_in_usd");
        if ($update_success == true) {
            $user = selectMap("select * from users where user_id = $user_id");
            $coin_create_response = http_json_post($server_url . "coin_create.php", array(
                "token" => $user["user_session_token"],
                "coin_name" => $coin_name,
                "coin_code" => $coin_code,
            ));
            if ($coin_create_response["message"] != null)
                send(1, "Create coin error", $coin_create_response["message"] . " " . $test_string);
        }
    } else {
        send(1, "Cannot find order message", $test_string);
    }
} else {
    send(1, "Cannot parse label in payment", $test_string);
}
