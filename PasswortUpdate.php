<?php
//Author: Andreas Blech
session_start();

//Inkludieren von script-Dateien
include './includes/db_connector.php';
include './includes/emailSender.php';

//Top Bereich inkludieren
require "./includes/_top.php";

//Prüfung, ob der Nutzer sich bereits eingeloggt hat.
if(!(isset($_SESSION['loggedIn']))) {
	$_SESSION['loggedIn'] = false;
}

//DB Funktionen, die später ausgelagert werden sollten

/*Liefert den Cryptkey zum Account, der zu der übergeben Email-Adresse gehört oder false*/
function getCryptkeyByMail($mail) {
	$db = db_connect();
	$sql = "SELECT cryptkey FROM Privacy WHERE idPrivacy = (SELECT idUser FROM User WHERE email = LOWER(?))";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('s',$mail);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);				
	if(isset($dbentry['cryptkey'])){
		return $dbentry['cryptkey'];
	}
	else {
		return false;
	}
	//return "asdfjklö"; //Testzwecke
}

/*Liefert das Registrierungsdatum zu einer UserID oder false*/
function regDateByCryptkey($cryptkey) {
	$db = db_connect();
	$sql = "SELECT regDate FROM User, Privacy WHERE idUser = idPrivacy AND cryptkey = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('s',$cryptkey);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	if(isset($dbentry['regDate'])){
		//echo 'RegDate '.$dbentry['regDate'];
		$dateTeile = explode(" ", $dbentry['regDate']); //Im Datestring ist auch die Zeit, wir wollen nur das Datum (siehe Erstellung des Benutzeraccounts)
		db_close($db);
		return $dateTeile[0];
	}
	else {
		echo "Error: ".mysqli_error($db);
		db_close($db);
		return false;
	}
}



/*Ändert das Passwort des zum Cryptkey gehörenden Accounts*/
/*Liefert true bei Erfolg und false beim Fehlerfall*/
function changePasswortByCryptkey($cryptkey, $newPasswort) {
	$date = regDateByCryptkey($cryptkey);	
	$pass_md5 = md5($newPasswort.$date);
	$db = db_connect();
	$sql = "UPDATE User SET password = ? WHERE idUser = (SELECT idPrivacy FROM Privacy WHERE cryptkey = ?)";
	$stmt = $db->prepare($sql);
	mysqli_stmt_bind_param($stmt, "ss", $pass_md5, $cryptkey);
	$stmt->execute();
	db_close($db);				
	
	return true; //Testzwecke
}

/*Liefert den Benutzernamen des Accounts, der zum cryptkey gehört*/
function getUserByCryptkey($cryptkey) {
	$db = db_connect();
	$sql = "SELECT username FROM User WHERE idUser = (SELECT idPrivacy FROM Privacy WHERE cryptkey = ?)";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('s',$cryptkey);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);				
	if(isset($dbentry['username'])){
		return $dbentry['username'];
	}
	else {
		return false;
	}
	//return "blecha"; //Testzwecke
}

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
				
				//$_SESSION['loggedIn'] = false;
				
				if($_SESSION['loggedIn']===false) { //Schauen, ob Nutzer schon eingeloggt ist
					if(isset($_GET['c'])) { //3. Fall: Der Link mit dem Cryptkey wurde aufgerufen 
						//Zeige Formular mit 2 Passwort Feldern ("Passwort" und "Passwort wiederholen")
						echo'<form action="PasswortUpdate.php" method="post">
									<center>
										<h2>Neues Passwort eingeben</h2>
										<table>
											<tr>
												<td><b>Passwort:</b></td>
												<td><input type="text" name="passwort"></td>
											</tr>
											<tr>
												<td><b>Passwort wiederholen:</b></td>
												<td><input type="text" name="passwortwdh"></td>
											</tr>
										</table>
									<input type="submit" value="Passwort ändern">
									</center>
								</form>';
							//Speichere den Cryptkey temporär in der Session, damit man ihn auslesen kann, wenn das Formular versendet wurde
							$_SESSION['cryptkey'] = $_GET['c'];	
					} elseif(isset($_POST['mail'])) { //2.Fall: Zeige Meldung und schicke Bestätigungslink
						echo '<h2>Passwort Ändern</h2>';
						//echo '<h3>Email gesendet an: '.$_POST['mail'].'</h3>';
						$cryptkey = getCryptkeyByMail($_POST['mail']);
						if($cryptkey != false) {
							$mailcontent = "<div style=\"margin-left:10%;margin-right:10%;background-color:#757575\">
									<img src=\"img/wLogo.png\" alt=\"TueGutes\" title=\"TueGutes\" style=\"width:25%\"/>
							</div>
							<div style=\"margin-left:10%;margin-right:10%\">
								<h2>Klicke auf den Link, um ein neues Passwort zu setzen http://localhost/git/PasswortUpdate.php?c=$cryptkey </h2>
							</div>";
							//"http://localhost/git/PasswortUpdate.php?c=$cryptkey";
							if(sendEmail($_POST['mail'], "TueGutes - Passwort Ändern", $mailcontent)===true) {
									echo '<h3><font color=green>Es wurde ein Passwort-Änderungs-Link an '.$_POST['mail'].' gesendet</font></h3><p>';
									echo '<h4>Klicken sie auf die URL in der Email, um das Passwort zu ändern</h4>';
							}
							else {
								//Das Senden der Email ist fehlgeschlagen
								echo '<h3><font color=red>Email an $mail konnte nicht gesendet werden</font></h3><p>';
							}
						}
						else {
							//Es wurde kein Account zu der angegebenen Email gefunden
							echo '<h3><font color=red>Fehler! Kein Account zu '.$_POST['mail'].' gefunden</font></h3><p>';
							echo'<form action="PasswortUpdate.php" method="post">
									<center>
										<table>
											<tr>
												<td><b>E-Mail Adresse:</b></td>
												<td><input type="text" name="mail"></td>
											</tr>
										</table>
									<input type="submit" value="Email senden">
									</center>
								</form>';	
						}
					} elseif(isset($_POST['passwort']) && isset($_POST['passwortwdh'])) {//4.Fall: Neues Passwort setzen, darüber benachrichtigen
						//Einloggen
						if($_POST['passwort'] != $_POST['passwortwdh']) {
							echo'<h3> Die Passwörter stimmen nicht überein</h3>';
							echo'<form action="PasswortUpdate.php" method="post">
									<center>
										<h2>Neues Passwort eingeben</h2>
										<table>
											<tr>
												<td><b>Passwort:</b></td>
												<td><input type="text" name="passwort"></td>
											</tr>
											<tr>
												<td><b>Passwort wiederholen:</b></td>
												<td><input type="text" name="passwortwdh"></td>
											</tr>
										</table>
									<input type="submit" value="Passwort ändern">
									</center>
								</form>';
						}
						else {
							changePasswortByCryptkey($_SESSION['cryptkey'], $_POST['passwort']);
							//TODO: Auf Profilseite weiterleiten
							$_SESSION['loggedIn'] = true;
							$_SESSION['user'] = getUserByCryptkey($_SESSION['cryptkey']); 
							unset($_SESSION['cryptkey']);
							echo'<h3>Passwort erfolgreich geändert</h3>';
						}
						//$_SESSION['loggedIn']=true;
						//Header("Location: ./");
					} else{ //1. Fall: Nutzer ist nicht eingeloggt und auf PasswortUpdate.php gelangt 
						echo '<h2>Passwort Ändern</h2>';
						echo'<form action="PasswortUpdate.php" method="post">
									<center>
										<table>
											<tr>
												<td><b>E-Mail Adresse:</b></td>
												<td><input type="text" name="mail"></td>
											</tr>
										</table>
									<input type="submit" value="Email senden">
									</center>
								</form>';	
					}
				} else { //5. Fall: Nutzer ist bereits eingeloggt
					echo '<h3>Du bist bereits eingeloggt</h3>';
					echo'<a href="./profile.php">Profil anzeigen</a>';
				}

				echo '<footer>';
					include "./includes/_bottom.php"; 	
				echo '</footer>';			
?>