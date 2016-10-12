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
		<h1>TueGutes Registration Panel</h1>

		<?php
			//Prüfung, ob das Formular bereits gesendet wurde
			if (isset($_POST['benutzername']) && isset($_POST['passwort']) && isset($_POST['passwortwdh']) && isset($_POST['mail']) && $_SESSION['user']==="null") {

				$user = $_POST['benutzername'];
				$pass = $_POST['passwort'];
				$passwdh = $_POST['passwortwdh'];
				$mail = $POST['mail'];

				//Prüfung, ob alle Felder ausgefüllt sind
				if ($user==="" OR $pass==="" OR $passwdh ==="" OR $mail ==="") {
					echo '<font color=red>Fehler! Bitte alle Felder ausfüllen!</font>';
					include 'Kontoerstellung.html';
				} else {
					if($pass != $passwdh) {
						echo '<font color=red> Fehler! Passwörter stimmen nicht überein</font><p>';
						include 'Kontoerstellung.html';
					}
					else {
					
						//Auslesen des Nutzers aus der Datenbank
						$db = db_connect();
						$sql = "SELECT * FROM Benutzer WHERE Benutzername = ?";
						$stmt = $db->prepare($sql);
						$stmt->bind_param('s',$user);
						$stmt->execute();
						$result = $stmt->get_result();
						//db_close($db);

						//Auslesen des Ergebnisses
						$dbentry = $result->fetch_assoc();
						if (isset($dbentry['Benutzername'])) {
							//Nutzer ist in der Datenbank vorhanden

							if (md5($pass . $dbentry['RegDatum'])==$dbentry['Passwort']) {
								//Korrektes Passwort angegeben
								$_SESSION['user'] = $user;
								header("Refresh:0"); //Seite aktualisieren
							} else {
								//Falsches Passwort angegeben
								echo '<font color=red>Fehler! Das eingegebene Passwort ist nicht korrekt!</font><p>';
								include 'Kontoerstellung.html';
							}
						}
					 	else {
							//Nutzer ist nicht in der Datenbank vorhanden
							echo '<font color=red>Fehler! Nutzer ' . $user . ' existiert nicht!</font><p>';
							include 'Kontoerstellung.html';
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

	</body>
</htlm>