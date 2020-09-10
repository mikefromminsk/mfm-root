<?php


spl_autoload_register(function ($class_name) {
    include $class_name . ".php";
});

use PHPMailer\PHPMailer;
use PHPMailer\Exception;
use PHPMailer\SMTP;

if (
    $email_addr == null
    || $email_pass == null
    || $email_addr == null
    || $email_name == null
)
    die(json_encode(array("message" => "Create properties.php with email parameters")));

function send($Subject, $Body, $Receivers){
    $mail = new PHPMailer(true);
    $mail->IsSMTP();
    $mail->SMTPDebug = 1;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "ssl";
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 465;
    $mail->Username = $GLOBALS["email_addr"];
    $mail->Password = $GLOBALS["email_pass"];
    $mail->From = $GLOBALS["email_addr"];
    $mail->FromName = $GLOBALS["email_name"];
    $mail->isHTML(true);

    if (is_array($Receivers)) {
        if (array_keys($Receivers) === range(0, count($Receivers) - 1)){
            foreach ($Receivers as $receiver)
                $mail->addAddress($receiver);
        } else {
            foreach ($Receivers as $receiver_email => $receiver_name)
            $mail->addAddress($receiver_email, $receiver_name);
        }
    } else {
        $mail->addAddress($Receivers);
    }

    $mail->Subject = $Subject;
    $mail->Body = $Body;

    return $mail->send();
}