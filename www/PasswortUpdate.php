<?php
/**
 * Bietet die Möglichkeit das Passwort für einen Account zu ändern ohne das alte zu kennen
 *
 * Ein nicht eingeloggter Nutzer gelangt auf die Seite und gibt entweder seinen Benutzernamen oder seine Email-Adresse ein
 * Anschließend wird eine Email an die zum Account gehörige Email-Adresse gesendet mit einer URL auf der man ein neues Passwort setzen kann
 *
 * @author     Andreas Blech <andreas.blech@stud.hs-hannover.de>
 */
 
require "./includes/DEF.php";

//Inkludieren von script-Dateien

//Top Bereich inkludieren
require "./includes/_top.php";

/*
Es gibt ... Fälle
1. Nutzer ist nicht eingeloggt und geht auf die PasswortUpdate Seite (über einen Link)
2. Der Nutzer hat das Formular mit einer E-Mail abgeschickt
	Es wird eine Nachricht diesbezüglich angezeigt und eine Mail mit dem Cryptkey gesendet
3. Der Nutzer hat auf den E-Mail Link geklickt und kommt auf ein
	Formular mit zwei Felder (Passwort und Passwort wdh.)
4. Das Formular mit einem neuen Passwort wurde abgeschickt, das neue Passwort wird 		gesetzt und der Nutzer wird über die Änderung informiert
5. Der Nutzer ist eingeloggt: <Placeholder>
*/

if(!$_USER->loggedIn()) { //Schauen, ob Nutzer schon eingeloggt ist
	if(isset($_GET['c'])) { //3. Fall: Der Link mit dem Cryptkey wurde aufgerufen
		//Zeige Formular mit 2 Passwort Feldern ("Passwort" und "Passwort wiederholen")
		echo'<form action="./PasswortUpdate" method="post">
				<center>
					<h2>Neues Passwort eingeben</h2>
					<table>
						<tr>
							<td><b>Passwort:</b></td>
							<td><input type="password" name="passwort"></td>
						</tr>
						<tr>
							<td><b>Passwort wiederholen:</b></td>
							<td><input type="password" name="passwortwdh"></td>
						</tr>
					</table>
				<input type="submit" value="Passwort ändern">
				</center>
			</form>';
		
		//Speichere den Cryptkey temporär in der Session, damit man ihn auslesen kann, wenn das Formular versendet wurde
		$_SESSION['cryptkey'] = $_GET['c'];
	} elseif(isset($_POST['UsernameOrMail'])) { //2.Fall: Zeige Meldung und schicke Bestätigungslink
		echo '<h2>Passwort Ändern</h2>';
		$mail = $_POST['UsernameOrMail'];
		$userID = DBFunctions::db_idOfBenutzername($_POST['UsernameOrMail']);
		if($userID === false) {
			//Falls es kein Username war, gehe davon aus, dass es eine Email-Adresse war.
			//Wenn es auch keine bekannte Email-Adresse war ist cryptekey später false
			$cryptkey = DBFunctions::db_getCryptkeyByMail($_POST['UsernameOrMail']);
		}
		else {
			$mail = DBFunctions::db_getMailOfBenutzerByID($userID);
			$cryptkey = DBFunctions::db_getCryptkeyByMail($mail);
		}

		if($cryptkey != false) {
			$actual_link = $HOST . '/PasswortUpdate';
			//$actual_link = explode('.', $actual_link)[0];
			$mailcontent = "<div style=\"margin-left:10%;margin-right:10%;background-color:#757575\">
					<img src=\"img/wLogo.png\" alt=\"TueGutes\" title=\"TueGutes\" style=\"width:25%\"/>
			</div>
			<div style=\"margin-left:10%;margin-right:10%\">
				<h2>Klicke auf den <a href=\"$actual_link?c=$cryptkey\">Link</a>, um ein neues Passwort zu setzen </h2>
			</div>";
			//"http://localhost/git/PasswortUpdate.php?c=$cryptkey";
			if(sendEmail($mail, "TueGutes - Passwort Ändern", $mailcontent)===true) {
				echo '<h3><font color=green>Es wurde ein Passwort-Änderungs-Link an '.$mail.' gesendet</font></h3><p>';
				echo '<h4>Klicken sie auf die URL in der Email, um das Passwort zu ändern</h4>';
			}
			else {
				//Das Senden der Email ist fehlgeschlagen
				echo "<h3><font color=red>Email an $mail konnte nicht gesendet werden</font></h3><p>";
			}
		}
		else {
			//Es wurde kein Account zu der angegebenen Email gefunden
			echo '<h3><font color=red>Fehler! Kein Account zu '.$_POST['UsernameOrMail'].' gefunden</font></h3><p>';
			echo'<form action="PasswortUpdate.php" method="post">
					<center>
						<br>
						<input type="text" size = 35 placeholder = "Benutzename oder Email-Adresse" name="UsernameOrMail"><br>
						<br>
						<input type="submit" value="Email senden">
					</center>
				</form>';
		}
	} elseif(isset($_POST['passwort']) && isset($_POST['passwortwdh'])) {//4.Fall: Neues Passwort setzen, darüber benachrichtigen
		//Einloggen
		if($_POST['passwort'] != $_POST['passwortwdh']) {
			echo'<h3> Die Passwörter stimmen nicht überein</h3>';
			echo'<form action="./PasswortUpdate" method="post">
					<center>
						<h2>Neues Passwort eingeben</h2>
						<table>
							<tr>
								<td><b>Passwort:</b></td>
								<td><input type="password" name="passwort"></td>
							</tr>
							<tr>
								<td><b>Passwort wiederholen:</b></td>
								<td><input type="password" name="passwortwdh"></td>
							</tr>
						</table>
					<input type="submit" value="Passwort ändern">
					</center>
				</form>';
		}
		else {
			DBFunctions::db_changePasswortByCryptkey($_SESSION['cryptkey'], $_POST['passwort']);
			unset($_SESSION['cryptkey']);
			echo'<h3>Passwort erfolgreich geändert</h3>';
		}
	} else{ //1. Fall: Nutzer ist nicht eingeloggt und auf PasswortUpdate.php gelangt
		echo '<h2>Passwort Ändern</h2>';
		echo '<h3>Gebe deinen Benutzernamen oder deine Email-Adresse ein';
		echo'<form action="./PasswortUpdate" method="post">
				<center>
						<br>
						<input type="text" size = 35 placeholder = "Benutzename oder Email-Adresse" name="UsernameOrMail"><br>
						<br>
						<input type="submit" value="Email senden">
				</center>
			</form>';
	}
} else { //5. Fall: Nutzer ist bereits eingeloggt
	echo '<h3>Du bist bereits eingeloggt</h3>';
	echo'<a href="./profile">Profil anzeigen</a>';
}

include "./includes/_bottom.php";
?>
