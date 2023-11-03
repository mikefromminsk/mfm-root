<?php

include_once "auth.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/PHPMailer/send.php";

$email = get_required(email);

$user = selectRowWhere(users, [user_id => $user_id]);

if ($user[email_cofirmed] == 1) error("email confirmed");

$email_confirmation_code = rand(100000, 999999);

updateWhere(users, [email_confirm_code => $email_confirmation_code, email => $email, email_confirmed => 0], [user_id => $user_id]);

$response[result] = send($email, "Confirmation $gmail_title","Your code is $email_confirmation_code");

echo json_encode($response);