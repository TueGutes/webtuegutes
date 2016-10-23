<?php

session_start();

//Prüfung, ob der Nutzer sich bereits eingeloggt hat.
if (!(isset($_SESSION['user']))) {
	$_SESSION['user'] = "null";
}

//Inkludieren von script-Dateien
include 'db_connector.php';

//DB Funktionen, die später ausgelagert werden sollten

//Gibt das Attribut idBenutzer zu einem gegebenen Benutzernamen zurück oder false,
//falls es keinen Account mit dem Benutzernamen gibt
function idToBenutzername(string benutzername) {
	$db = db_connect();
	$sql = "SELECT idBenutzer FROM Benutzer WHERE Benutzername = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('s',$benutzername);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);
	if(isset($dbentry['idBenutzer'])){
		return $dbentry['idBenutzer'];
	}
	else {
		return false;
	}
}

//Gibt das Attribut idBenutzer zu einer gegebenen email Adresse zurück oder false, falls
//es keinen Account mit dieser Emailadresse gibt
function idToEmailAdresse(string emailadresse) {
	$db = db_connect();
	$sql = "SELECT idBenutzer FROM Benutzer WHERE Email = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('s',$emailadresse);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);				
	if(isset($dbentry['idBenutzer'])){
		return $dbentry['idBenutzer'];
	}
	else {
		return false;
	}
}


?>

<html>
	<head>
		<meta charset="UTF-8">
		<title>Registration</title>
	</head>

	<body>
		<?php include "top.php";?>
		<div style="margin-left:25%;margin-right:25%;min-height:100%">
			<center>
			
			<?php
				if(isset($_GET['e'])) {
					$user = base64_decode($_GET['e']);
					$db = db_connect();
					$sql = "SELECT * FROM Benutzer WHERE Benutzername = ?";
					$stmt = $db->prepare($sql);
					$stmt->bind_param('s',$user);
					$stmt->execute();
					$result = $stmt->get_result();
					$dbentry = $result->fetch_assoc();
					
					if(isset($dbentry['Benutzername'])) {
						//TODO der Status muss auf sowas wie "aktiviert gesetzt werden"
						//Außerdem muss man beim Login auf den Status des Accounts achten...
						$sql = "UPDATE Benutzer Set Status = 1 WHERE Benutzername = ?";
						$stmt = $db->prepare($sql);
						$stmt->bind_param('s',$user);
						//$stmt->execute();
						
						echo '<h1>Deine Registrierung war erfolgreich '.$user.'!</h1>' ;
						$_SESSION['user'] = $user;
						echo '(<a href="./">Zur Startseite</a>)';
						
						//echo 'Benutzer bekannt';
					}
					else {
						echo '<font color=red>Link broken :(    Try again...</font><p>';
					}
					
					//echo '<h1>Deine Registrierung war erfolgreich '.$user.'!</h1>' ;
					//$_SESSION['user'] = $user;
					//echo '<font color=green>Registration war erfolgreich!</font><p>';
					//echo '(<a href="./">Zur Startseite</a>)';
					
					//Header("Location: ./");
				}
				//Prüfung, ob das Formular bereits gesendet wurde
				elseif (isset($_POST['benutzername']) && isset($_POST['passwort']) && isset($_POST['passwortwdh']) && isset($_POST['mail']) &&isset($_POST['vorname']) && isset($_POST['nachname']) && $_SESSION['user']==="null") {

					$user = $_POST['benutzername'];
					$vorname = $_POST['vorname'];
					$nachname = $_POST['nachname'];
					$pass = $_POST['passwort'];
					$passwdh = $_POST['passwortwdh'];
					$mail = $_POST['mail'];
					//Prüfung, ob alle Felder ausgefüllt sind
					if ($user==="" OR $pass==="" OR $passwdh ==="" OR $mail ==="" OR $vorname ==="" OR $nachname==="") {
						echo '<font color=red>Fehler! Bitte alle Felder ausfüllen!</font><p>';
						include 'Kontoerstellung.html';
					} else {
						$db = db_connect();
						$sql = "SELECT * FROM Benutzer WHERE Benutzername = ?";
						$stmt = $db->prepare($sql);
						$stmt->bind_param('s',$user);
						$stmt->execute();
						$result = $stmt->get_result();
						//Auslesen des Ergebnisses
						$dbentry = $result->fetch_assoc();
						if (isset($dbentry['Benutzername'])) { //Schauen, ob Benutzername bereits existiert
							echo'<font color=red>Fehler! Benutzername bereits vorhanden!</font><p>';
							echo '<a href="./PasswortUpdate.php">Passwort vergessen?</a>';
							include 'Kontoerstellung.html';
							db_close($db);
						}
						elseif($pass != $passwdh) {	//Prüfen, ob Passwörter übereinstimmen
							echo '<font color=red> Fehler! Passwörter stimmen nicht überein</font><p>';
							include 'Kontoerstellung.html';
							db_close($db);
						}
						else {
							//Auslesen des Nutzers aus der Datenbank
							$db = db_connect();
							$sql = "SELECT * FROM Benutzer WHERE Email = ?";
							$stmt = $db->prepare($sql);
							$stmt->bind_param('s',$mail);
							$stmt->execute();
							$result = $stmt->get_result();
							//Auslesen des Ergebnisses
							$dbentry = $result->fetch_assoc();
							
							if (isset($dbentry['Email'])) {
								//Email wurde bereits registriert
								echo '<font color = red>Fehler! Diese Email-Adresse wurde bereits registriert</font><p>';
								echo '<a href="./PasswortUpdate.php">Passwort vergessen?</a>';
								include 'Kontoerstellung.html';
								db_close($db);
							}
						 	else {
								//SUCCESS! - Alle Parameter sind korrekt -> neuen Eintrag in Datenbank vornehmen
														
								//Benutzer in die Datenbank einfügen
								$sql = "Insert into Benutzer (Benutzername, Vorname, Nachname, Passwort, Email, RegDatum) values(?,?,?,?,?,?)";
								$stmt = $db->prepare($sql);
								$date = date("Y-m-d");
								$pass_md5 = md5($pass.$date);
								mysqli_stmt_bind_param($stmt, "ssssss", $user, $vorname, $nachname, $pass_md5, $mail, $date);
								$stmt->execute();
								//$_SESSION['user'] = $user; //noch nicht gleich einloggen, erst auf Bestätigungslink Aufruf in Email warten
								$affected_rows = mysqli_stmt_affected_rows($stmt);
								if($affected_rows == 1) {
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
									//$mail->msgHTML(file_get_contents('email_registrierung.php'), dirname(__FILE__));
									$phpmail->AltBody = 'This is a plain-text message body'; //Alt = Alternative
									//$mail->Body = 'This is a test.';
									//send the message, check for errors
									if (!$phpmail->send()) {
 									   echo "ERROR: Sending Mail " . $phpmail->ErrorInfo;
									}
									else {
										echo '<font color=green>Bestätigungslink wurde gesendet an: '.$mail.'</font><p>';
									}
									
									
									//echo 'Success'.$affected_rows;
								}
								else {
									echo '<font color=red>internal Database error</font><p>';
									
								}
								
								db_close($db);
								
								echo '(<a href="./">Zur Startseite</a>)';
								
								//Funktioniert nicht, da lokaler Server :(
								//mail($mail, 'TueGutes Registration', 'Du wurdest registriert');
								//include 'Kontoerstellung.html';
							}
						}
					}
				} else {
					if ($_SESSION['user']==="null") {
						echo '<h1><p>Einmal anmelden und Gutes tun!</p></h1>';
						//Wenn der Nutzer nicht eingeloggt ist und das Formular noch nicht abgeschickt wurde.
						echo 'Geben Sie Ihre gewünschten Nutzerdaten ein, um sich im System zu registrieren.<p>';
						include 'Kontoerstellung.html';
					} else {
						//Wenn der Nutzer bereits eingeloggt ist.
						Header("Location: ./");
					}
				}
			?>
		</div>
	</body>
</htlm>