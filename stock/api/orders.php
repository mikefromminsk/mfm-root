<?php

include_once "auth.php";

$response["active"] =  select("select * from orders where user_id = $user_id and status = 0 order by timestamp DESC");
$response["history"] = select("select * from orders where user_id = $user_id and status <> 0 order by timestamp DESC");

echo json_encode($response);