<?php

/*Sends an email from tuegutesinhannover@gmail.com to $to with subject $subject and
HTML Content $content*/
function sendEmail($to, $subject, $content) {
	require 'PHPMailer-master/PHPMailerAutoload.php';
	$phpmail = new PHPMailer;
	$phpmail->isSMTP();
	$phpmail->SMTPSecure = 'ssl';
	$phpmail->SMTPAuth = true;
	$phpmail->Host = 'smtp.gmail.com';
	$phpmail->Port = 465;
	$phpmail->Username = 'tuegutesinhannover@gmail.com';
	$phpmail->Password = 'TueGutes1234';
	//$mail->setFrom('Tue Gutes in Hannover');
	$phpmail->CharSet = 'utf-8';
	$phpmail->setFrom('tuegutesinhannover@gmail.com');
	$phpmail->addAddress($to);
	//$mail->addAddress('Andreas.blech@t-online.de');
	$phpmail->Subject = $subject;
	$phpmail->msgHTML($content);
	$phpmail->AltBody = 'This is a plain-text message body';
	//send the message, check for errors
	if (!$phpmail->send()) {
 		//echo "ERROR: Sending Mail " . $phpmail->ErrorInfo;
 		return false;
	}
	else {
		return true;
	}
}

?>
