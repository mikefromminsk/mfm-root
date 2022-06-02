<?php

include_once "auth.php";

$response[transaction_history] =
    select("select * from transfers where from_user_id = $user_id or to_user_id = $user_id order by time DESC limit 10");

echo json_encode($response);