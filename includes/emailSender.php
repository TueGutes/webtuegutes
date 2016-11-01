<?php 

require './includes/DEF.php';

/*Sends an email from tuegutesinhannover@gmail.com to $to with subject $subject and
HTML Content $content*/
function sendEmail($to, $subject, $content) {
	if($USE_GMAIL === true) {
		/*Nutze den Gmail-Account, um die Mails zu senden*/
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
	else {
		/*Nutze die PHP mail funktion */
		//$to      = 'daniel.kadenbach@hs-hannover.de';
		//$subject = 'the subject';
		//$message = 'hello';
		$headers = 'From: proanvil@hs-hannover.de' . "\r\n" .'X-Mailer: PHP/' . phpversion(). "\r\n";
		
		// für HTML-E-Mails muss der 'Content-type'-Header gesetzt werden
		$header  = 'MIME-Version: 1.0' . "\r\n";
		$header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		mail($to, $subject, $content, $headers);
		return true;
	}
	/*"<div style=\"margin-left:10%;margin-right:10%;background-color:#757575\"><img src=\"img/logo_provisorisch.png\" alt=\"Zurück zur Startseite\" title=\"Zurück zur Startseite\" style=\"width:25%\"/></div><div style=\"margin-left:10%;margin-right:10%\"><h1>Herzlich Willkommen <b>".$vorname."</b> bei 'Tue Gutes in Hannover':</h1> <h3>Klicke auf den Link, um deine Registrierung abzuschließen: http://localhost/git/registration.php?e=".base64_encode($user)." </h3></div>"*/
	
}

?>