<?php

require("PHPMailer/Exception.php");
require("PHPMailer/PHPMailer.php");
require("PHPMailer/SMTP.php");

function send($to, $subject, $body, $message_type = null, $message_object_id = null)
{

    $to_user_id = null;
    if (is_numeric($to)) {
        $to_user_id = $to;
        $to = scalar("select user_login from users where user_id = $to_user_id");
    } else if (is_string($to)) {
        $to_user_id = scalar("select user_id from users where user_login = '" . uencode($to) . "'");
    }

    file_put_contents("send.log", date("Y-m-d H:i:s") . " $to: $subject\n$body\n\n", FILE_APPEND);

    if ($to_user_id != null) {
        insertList("messages", array(
            "user_id" => $to_user_id,
            "message_title" => $subject,
            "message_text" => $body,
            "message_type" => $message_type,
            "message_object_id" => $message_object_id,
        ));
    }
    if ($GLOBALS["email_server_secure"] != null && !extension_loaded('openssl'))
        return "openssl not available";
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->IsSMTP();
        $mail->Host = $GLOBALS["email_server_host"];
        $mail->SMTPSecure = $GLOBALS["email_server_security"];
        $mail->Port = $GLOBALS["email_server_port"];
        $mail->SMTPAuth = true;
        $mail->Username = $GLOBALS["email_login"];
        $mail->Password = $GLOBALS["email_password"];
        $mail->SetFrom($to == $GLOBALS["email_login"] ? $GLOBALS["email_login"] : $to);
        $mail->IsHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AddAddress($to);
        $mail->send();
        return true;
    } catch (PHPMailer\PHPMailer\Exception $e) {
        return $e->getTraceAsString();
    }
}