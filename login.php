<?php

session_start();

//Prüfung, ob der Nutzer sich bereits eingeloggt hat.
if (!(isset($_SESSION['user']))) {
	$_SESSION['user'] = "null";
}

//Inkludieren von script-Dateien
include 'db_connector.php';

	//Prüfung, ob das Formular bereits gesendet wurde
	if (isset($_POST['benutzername']) && isset($_POST['passwort']) && $_SESSION['user']==="null") {

		$user = $_POST['benutzername'];
		$pass = $_POST['passwort'];

		//Prüfung, ob alle Felder ausgefüllt sind
		if ($user==="" OR $pass==="") {
			echo '<font color=red>Fehler! Bitte alle Felder ausfüllen!</font>';
			include 'login.html';
		} else {
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
					header("Refresh:0");
				} else {
					//Falsches Passwort angegeben
					echo '<font color=red>Fehler! Das eingegebene Passwort ist nicht korrekt!</font><p>';
					include 'login.html';
				}
			} else {
				//Nutzer ist nicht in der Datenbank vorhanden
				echo '<font color=red>Fehler! Nutzer ' . $user . ' existiert nicht!</font><p>';
				include 'login.html';
			}
		}
	} else {
		if ($_SESSION['user']==="null") {
			//Wenn der Nutzer nicht eingeloggt ist und das Formular noch nicht abgeschickt wurde.
			include 'login.html';
		} else {
			//Wenn der Nutzer bereits eingeloggt ist.
			echo 'Sie sind eingeloggt als '.$_SESSION['user']." (<a href=\"logout.php/?source=login.php\">Logout</a>)";
		}
	}