<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/messenger/properties.php";

/*if (
    $email_addr == null
    || $email_pass == null
    || $email_addr == null
    || $email_name == null
)
    die(json_encode(array("message" => "Create properties.php with email parameters")));*/

spl_autoload_register(function ($class_name) {
    include_once $_SERVER["DOCUMENT_ROOT"] . "/$class_name.php";
});

use PHPMailer\PHPMailer;
use PHPMailer\Exception;
use PHPMailer\SMTP;


function mailSend($emails, $subject, $body){
    return true;
    try {
        $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465;
        $mail->Username = $GLOBALS["email_addr"];
        $mail->Password = $GLOBALS["email_pass"];
        $mail->From = $GLOBALS["email_addr"];
        $mail->FromName = $GLOBALS["email_name"];
        $mail->isHTML(true);

        if (is_array($emails)) {
            if (array_keys($emails) === range(0, count($emails) - 1))
                foreach ($emails as $receiver)
                    $mail->addAddress($receiver);
             else
                foreach ($emails as $receiver_email => $receiver_name)
                    $mail->addAddress($receiver_email, $receiver_name);
        } else {
            $mail->addAddress($emails);
        }

        $mail->Subject = $subject;
        $mail->Body = $body;
        $success = $mail->send();
        if (!$success && $mail->SMTPDebug == 1)
            return "check google option at https://myaccount.google.com/lesssecureapps";
    } catch (Exception $e) {
        return $e->getMessage();
    }
    return $success;
}