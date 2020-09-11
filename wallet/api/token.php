<?php

include_once "db.php";

$user_token = get_required("token");

$user_id = scalar("select user_id from users where user_token = $user_token");

if ($user_id == null)
    error("token expired");