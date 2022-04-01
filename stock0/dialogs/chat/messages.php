<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock0/auth.php";

$dialog_id = get_required("dialog_id");

description(basename(__FILE__));

$response["messages"] = dataGet(["dialogs", $dialog_id, "messages"], $pass, null, -10);

echo json_encode($response);