<?php

include_once "db.php";

$email = get_required("email");

$new_token = random_id();

$success = updateWhere("users", array("user_token" => $new_token), array("user_email" => $email));

if (!$success)
    insertRow("users", array("user_email" => $email, "user_token" => $new_token));



