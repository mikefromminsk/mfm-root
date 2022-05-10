<?php
require_once "Exception.php";
require_once "PHPMailer.php";
require_once "SMTP.php";

$gmail_email = "mailtest82423@gmail.com";
$gmail_password = "123123QQQ";
$gmail_title = "mailtest";
//https://myaccount.google.com/lesssecureapps

function send($to, $subject, $body)
{
    try {
        $mail = new PHPMailer\PHPMailer(true);
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465;
        $mail->IsHTML(true);
        $mail->Username = $GLOBALS["gmail_email"];
        $mail->Password = $GLOBALS["gmail_password"];
        $mail->SetFrom($GLOBALS["gmail_email"], $GLOBALS["gmail_title"]);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AddAddress($to);
        $mail->send();
        return true;
    } catch (PHPMailer\Exception $e) {
        error($e->getMessage());
        return false;
    }
}