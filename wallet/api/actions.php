<?php

include_once "token.php";

$response = select("select * from actions where user_sender = $user_id");

echo json_encode_readable($response);