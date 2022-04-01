<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock0/auth.php";

/*TODO offset count*/

description(basename(__FILE__));

$dialogs = dataGet(["users", $login, "dialogs"], $pass, null, -10);

$response["dialogs"] = [];

foreach ($dialogs as $dialog_id => $unread_messages) {
    $dialog = dataGet(["dialogs", $dialog_id], $pass);
    $dialog["unread_messages"] = $unread_messages;
    $response["dialogs"][] = $dialog;
}

echo json_encode($response);