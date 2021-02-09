<?php

include_once "db.php";
include_once  $_SERVER["DOCUMENT_ROOT"] . "/PHPMailer/mail.php";

$email = get_required("email");

$new_token = random_id();

$success = updateWhere("users", array("user_token" => $new_token), array("user_email" => $email));

if (!$success)
    insertRow("users", array("user_email" => $email, "user_token" => $new_token));

$base_url = ($_SERVER['HTTPS'] == "on" ? "https://" : "http://") . $host_name;
$login_url = "$base_url/wallet/buy.php?token=$new_token";
$message = "<a href='$login_url'>$login_url</a>";

if (send("New Access token", $message, $email) == false)
    error("send message error");


