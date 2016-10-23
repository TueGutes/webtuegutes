<?php
require 'PHPMailer-master/PHPMailerAutoload.php';
$mail = new PHPMailer;
$mail->isSMTP();
$mail->SMTPSecure = 'ssl';
$mail->SMTPAuth = true;
$mail->Host = 'smtp.gmail.com';
$mail->Port = 465;
$mail->Username = 'tuegutesinhannover@gmail.com';
$mail->Password = 'TueGutes1234';
$mail->setFrom('tuegutesinhannover@gmail.com');
$mail->addAddress('Andreas.blech@t-online.de');
$mail->Subject = 'Ihre Registrierung bei TueGutes in Hannover"';

$mail->msgHTML("<h1>Herzlich Willkommen bei 'Tue Gutes in Hannover':</h1> <h3>Klicke auf den Link, um deine Registrierung abzuschließen: localhost/git/registration.php?e=".base64_encode('Andreas B.')."</h3>");
//$mail->msgHTML(file_get_contents('email_registrierung.php'), dirname(__FILE__));
$mail->AltBody = 'This is a plain-text message body'; //Alt = Alternative
//$mail->Body = 'This is a test.';
//send the message, check for errors
if (!$mail->send()) {
    echo "ERROR: " . $mail->ErrorInfo;
} else {
    echo "SUCCESS";
}