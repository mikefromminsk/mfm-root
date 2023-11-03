<?php

include_once "auth.php";

$email_confirm_code = get_required(email_confirm_code);

$user = selectRowWhere(users, [user_id => $user_id]);

if ($user[email_cofirmed] == 1) error("email confirmed");
if ($user[email_confirm_code] != $email_confirm_code) error("email email_confirmation_code is bad");

$response[result] = updateWhere(users, [email_confirmed => 1], [user_id => $user_id]);

echo json_encode($response);