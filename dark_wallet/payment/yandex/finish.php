<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/PHPMailer/mail.php";

//$_GET = json_decode(file_get_contents("test.json"), true);
file_put_contents("test.json", json_encode($_POST));

$notification_type = get_required("notification_type");
$operation_id = get_required("operation_id");
$amount = get_required("amount");
$withdraw_amount = get_required("withdraw_amount");
$currency = get_required("currency");
$sender = get_required("sender");
$codepro = get_required("codepro");
$label = get_required("label");
$sha1_hash = get_required("sha1_hash");
$unaccepted = get_required("unaccepted");
$datetime = get_required("datetime");

// chck servername

if (sha1("$notification_type&$operation_id&$amount&$currency&$datetime&$sender&$codepro&$yandex_secret_key&$label") != $sha1_hash)
    error("sha sum is not correct");

$success = updateWhere("actions", array(
    "action_complete" => "1"
), array(
    "action_id" => $label,
    "action_amount" => $amount,
));

if (!$success) {
    $admin_email = scalar("select user_email from users left join users where user_id = 1");
    send("Transaction failed", json_encode($_POST), $admin_email);
    error("action not found");
}

$action = selectRow("select * from actions where action_id = $label");
send("Transaction $label is completed", "Transaction $label is completed", [$action["user_sender"], $admin_email]);

