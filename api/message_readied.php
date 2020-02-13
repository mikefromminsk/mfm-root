<?php

include_once "login.php";

$message_id = get_required("message_id");

updateList("messages", array("message_readied" => 1), "message_Id", $message_id);

echo json_encode(array(
    "message" => null
));
