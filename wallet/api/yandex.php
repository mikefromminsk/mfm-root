<?php

include_once "db.php";

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

