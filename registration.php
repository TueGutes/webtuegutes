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
		<title>Registration</title>
	</head>

	<body>
		<?php include "top.php";?>
		<div style="margin-left:25%;margin-right:25%;min-height:100%">
			<center>
			
			<h1><p>Einmal anmelden und Gutes tun!</p></h1>

			<?php
				//Prüfung, ob das Formular bereits gesendet wurde
				if (isset($_POST['benutzername']) && isset($_POST['passwort']) && isset($_POST['passwortwdh']) && isset($_POST['mail']) && $_SESSION['user']==="null") {

					$user = $_POST['benutzername'];
					$pass = $_POST['passwort'];
					$passwdh = $_POST['passwortwdh'];
					$mail = $_POST['mail'];

					//Prüfung, ob alle Felder ausgefüllt sind
					if ($user==="" OR $pass==="" OR $passwdh ==="" OR $mail ==="") {
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
								include 'Kontoerstellung.html';
								db_close($db);
							}
						 	else {
								//SUCCESS! - Alle Parameter sind korrekt -> neuen Eintrag in Datenbank vornehmen
														
								//Benutzer in die Datenbank einfügen
								$sql = "Insert into Benutzer (Benutzername, Passwort, Email, RegDatum) values(?,?,?,?)";
								$stmt = $db->prepare($sql);
								/*$stmt->bind_param('s',$user);
								$stmt->bind_param('s',$pass);
								$stmt->bind_param('s',$mail);
								$stmt->bind_param('s'."");*/
								$date = date("Y-m-d");
								$pass_md5 = md5($pass.$date);
								mysqli_stmt_bind_param($stmt, "ssss", $user, $pass_md5, $mail, $date);
								$stmt->execute();
								$_SESSION['user'] = $user;
								$affected_rows = mysqli_stmt_affected_rows($stmt);
								if($affected_rows == 1) {
									echo '<font color=green>Registration war erfolgreich!</font><p>';

									//echo 'Success'.$affected_rows;
								}
								else {
									echo '<font color=red>internal Database error</font><p>';
									
								}
								
								db_close($db);
								
								echo '(<a href="./">Zur Startseite</a>)';
								
								//Funktioniert nicht, da lokaler Server :(
								mail($mail, 'TueGutes Registration', 'Du wurdest registriert');
								//include 'Kontoerstellung.html';
							}
						}
					}
				} else {
					if ($_SESSION['user']==="null") {
						//Wenn der Nutzer nicht eingeloggt ist und das Formular noch nicht abgeschickt wurde.
						echo 'Geben Sie Ihre gewünschten Nutzerdaten ein, um sich im System zu registrieren.<p>';
						include 'Kontoerstellung.html';
					} else {
						//Wenn der Nutzer bereits eingeloggt ist.
						echo 'Sie sind eingeloggt als '.$_SESSION['user']." (<a href=\"logout.php/?source=login.php\">Logout</a>)";
					}
				}
			?>
		</div>
	</body>
</htlm>