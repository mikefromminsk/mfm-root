<?php

include_once "db.php";
include_once "const.php";

$sha1_hash = get_required("sha1_hash");

$notification_type = get_required("notification_type");
$operation_id = get_required("operation_id");
$amount = get_required("amount");
$currency = get_required("currency");
$datetime = get_required("datetime");
$sender = get_required("sender");
$codepro = get_required("codepro");
$label = get_required("label");
$notification_secret = $yandex_money_secret_code;

$test_string = "$notification_type&$operation_id&$amount&$currency&$datetime&$sender&$codepro&$notification_secret&$label";
$test_hash = hash("sha1", $test_string);

if ($sha1_hash != $test_hash)
    db_error(USER_ERROR, "receipt is invalid");

$order_id = $label;
$order_message = selectMap("select * from messages where message_type = '" . MESSAGE_ORDER_CREATE . "' and message_object_id = $order_id");

if ($order_message != null) {
    send($order_message["user_id"], "Success paid", "Your order №$order_id has been paid", MESSAGE_ORDER_SUCCESS_PAID, $order_id);
} else {
    // thx for donate)
}
