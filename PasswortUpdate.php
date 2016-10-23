<?php

session_start();

//Prüfung, ob der Nutzer sich bereits eingeloggt hat.
if (!(isset($_SESSION['user']))) {
	$_SESSION['user'] = "null";
}

//Inkludieren von script-Dateien
include 'db_connector.php';
?>

<html>
	<head>
		<meta charset="UTF-8">
		<title>Passwort vergessen</title>
	</head>

	<body>
		<?php include "top.php";?>
		<div style="margin-left:25%;margin-right:25%;min-height:100%">
			<center>
			<?php
				if(isset($_GET['c']) && isset($_GET['p'])) {
					//Bestätigungslink wurde aufgerufen
					//Extrahiere Account CryptKey und neues Passwort und setze das neue Passwort
					
					
				}
				elseif(isset($_POST['mail']) && isset($_POST['passwort']) && isset($_POST['passwortwdh'])) {
					//Das Formular wurde abgeschickt. Es wird geprüft, ob es einen Account zu der Email-Adresse gibt und ob die Passwörter übereinstimmen
					//Falls alles erfolgreich war wird eine Passwort-Reset Mail gesendet
					$mail = $_POST['mail'];
					$pass = $_POST['passwort'];
					$passwdh = $_POST['passwortwdh'];
					
					$db = db_connect();
					$sql = "SELECT * FROM Benutzer WHERE Email = ?";
					$stmt = $db->prepare($sql);
					$stmt->bind_param('s',$mail);
					$stmt->execute();
					$result = $stmt->get_result();
					//Auslesen des Ergebnisses
					$dbentry = $result->fetch_assoc();
					db_close($db);
					if (!isset($dbentry['Email'])) {
						echo '<font color=red>Zu dieser Email Adresse ist uns kein Account bekannt</font><p>';
						include 'passwortAnfordern.html';
					}
					elseif($_POST['passwort'] != $_POST['passwortwdh']) {
						echo'<font color=red>Passwörter stimmen nicht überein!</font><p>';
						include 'passwortAnfordern.html';
					}
					else {
						//Alles in Ordnung -> Sende Mail
						//Bestätigungslink besteht aus CryptKey und base64 codiertem neuen Passwort
						echo $_POST['mail']." - ".$_POST['passwort']." - ".$_POST['passwortwdh'];
						
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
						$phpmail->addAddress($mail);
						//$mail->addAddress('Andreas.blech@t-online.de');
						$phpmail->Subject = 'Ihre Registrierung bei TueGutes in Hannover"';
						$phpmail->msgHTML("<div style=\"margin-left:10%;margin-right:10%;background-color:#757575\"><img src=\"img/logo_provisorisch.png\" alt=\"Zurück zur Startseite\" title=\"Zurück zur Startseite\" style=\"width:25%\"/></div><div style=\"margin-left:10%;margin-right:10%\"><h1>Herzlich Willkommen <b>".$vorname."</b> bei 'Tue Gutes in Hannover':</h1> <h3>Klicke auf den Link, um deine Registrierung abzuschließen: http://localhost/git/registration.php?e=".base64_encode($user)." </h3></div>");
						$phpmail->AltBody = 'This is a plain-text message body'; //Alt = Alternative
						
						//send the message, check for errors
						if (!$phpmail->send()) {
 						   echo "ERROR: Sending Mail " . $phpmail->ErrorInfo;
						}
						else {
							echo '<font color=green>Bestätigungslink wurde gesendet an: '.$mail.'</font><p>';
						}			
					}
				}
				elseif($_SESSION['user']==="null") {
					//Nutzer ist nicht eingeloggt und hat das Formular noch nicht abgeschickt
					include 'passwortAnfordern.html';
				}
				else {
					//Der Nutzer ist bereits eingeloggt. Das "Passwort Vergessen" Menü ergibt keinen Sinn
					//Leite weiter auf Startseite, vielleicht lieber persönliches Profil
					header('Location: ./');
				}
			?>
		</div>
	</body>
</htlm>