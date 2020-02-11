<?php

include_once "login.php";

echo json_encode(array(
    "user_login" => $user["user_login"],
    "user_session_token" => $user["user_session_token"],
    "user_stock_token" => $user["user_stock_token"],
));