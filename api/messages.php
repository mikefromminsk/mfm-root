<?php

include_once "login.php";

echo  json_encode(array(
    "messages" => select("select * from messages where user_id = $user_id limit 20")
));