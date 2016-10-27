<?php

session_start();

//Prüfung, ob der Nutzer sich bereits eingeloggt hat.
if(!(isset($_SESSION['loggedIn']))) {
	$_SESSION['loggedIn'] = false;
}

//Inkludieren von script-Dateien
include './includes/db_connector.php';
include './includes/emailSender.php';

//DB Funktionen, die später ausgelagert werden sollten

//Gibt das Attribut idBenutzer zu einem gegebenen Benutzernamen zurück oder false,
//falls es keinen Account mit dem Benutzernamen gibt
function idOfBenutzername($benutzername) {
	/*$db = db_connect();
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
	}*/
	
	return false; //Testzwecke
}

//Gibt das Attribut idBenutzer zu einer gegebenen email Adresse zurück oder false, falls
//es keinen Account mit dieser Emailadresse gibt
function idOfEmailAdresse($emailadresse) {
	/*$db = db_connect();
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
	}*/
	
	return false; //Testzwecke
}

function createBenutzerAccount($benutzername, $vorname, $nachname, $email, $passwort) {
	//TODO: Datenbank Insert ausarbeiten
	/*$sql = "Insert into Benutzer (Benutzername, Vorname, Nachname, Passwort, Email, RegDatum) values(?,?,?,?,?,?)";
	$stmt = $db->prepare($sql);
	$date = date("Y-m-d");
	$pass_md5 = md5($pass.$date);
	mysqli_stmt_bind_param($stmt, "ssssss", $user, $vorname, $nachname, $pass_md5, $mail, $date);
	$stmt->execute();
	
	$affected_rows = mysqli_stmt_affected_rows($stmt);
	if($affected_rows == 1) {
		return true;	
	}
	return false;*/
	
	return true; //Für Testzwecke
}


?>

<?php
require "./includes/_top.php";

				/*
				Es gibt ... Fälle:
				1. Nutzer ist nicht eingeloggt und gelangt auf die Registrierungsseite
					Es werden ihm die Formularfelder angezeigt
				2. Nutzer hat das Formular abgeschickt
					Daten werden geprüft und u.U. wird eine Mail gesendet
					2.1 Nutzer bekommt Nachricht: "Email erfolgreich gesendet" und ein Account mit dem 
					Status "unverifiziert" wird angelegt
					2.2 Nutzer wird informiert, dass etwas mit den Daten nicht stimmt und bekommt das gleiche Formular wieder vorgelegt
				3. Nutzer hat auf den Bestätigungslink geklickt.
					Der Account der zum Link gehört (Parameter) wird auf den Status "verified" gesetzt
				4. Der Nutzer ist bereits eingeloggt <Placeholder>
				*/
			
			
				if($_SESSION['loggedIn'] === false) {
					if(isset($_GET['c'])) {//3. Fall: Bestätigungslink
						
					
					} elseif(isset($_POST['benutzername']) && isset($_POST['passwort']) && isset($_POST['passwortwdh']) && isset($_POST['mail']) &&isset($_POST['vorname']) && isset($_POST['nachname'])) {  //2. Fall: Nutzer hat das Formular abgeschickt
						$user = $_POST['benutzername'];
						$vorname = $_POST['vorname'];
						$nachname = $_POST['nachname'];
						$pass = $_POST['passwort'];
						$passwdh = $_POST['passwortwdh'];
						$mail = $_POST['mail'];
						//Prüfung, ob alle Felder ausgefüllt sind
						if ($user==="" OR $pass==="" OR $passwdh ==="" OR $mail ==="" OR $vorname ==="" OR $nachname==="") {
							//2.1 Fall: Nicht alle Felder ausgefüllt
							echo '<font color=red>Fehler! Bitte alle Felder ausfüllen!</font><p>';
							include 'Kontoerstellung.html';
						} elseif (idOfBenutzername($user) != false){
							//2.2 Fall: Benutzername existiert bereits
							echo '<font color=red>Fehler! Benutzername bereits vergeben</font><p>';
							include 'Kontoerstellung.html';
						} elseif ($pass != $passwdh) { 
							//2.3 Fall: Passwörter sind nicht identisch
							echo '<font color=red> Fehler! Passwörter stimmen nicht überein</font><p>';
							include 'Kontoerstellung.html';
						} elseif(idOfEmailAdresse($mail) != false) { //Email-Adresse bereits registriert
							echo '<font color=red> Fehler! Email-Adresse bereits registriert</font><p>';
							include 'Kontoerstellung.html';
						} else {//Alles okay, erstelle neuen Account und sende Bestätigungsmail
							if(createBenutzerAccount($user, $vorname, $nachname, $mail, $pass) === true) {
								//Account erfolgreich in Datenbank erstellt
								//Sende Bestätigungslink an Mailadresse
								
								$mailcontent = "<div style=\"margin-left:10%;margin-right:10%;background-color:#757575\"><img src=\"img/logo_provisorisch.png\" alt=\"Zurück zur Startseite\" title=\"Zurück zur Startseite\" style=\"width:25%\"/></div><div style=\"margin-left:10%;margin-right:10%\"><h1>Herzlich Willkommen <b>".$vorname."</b> bei 'Tue Gutes in Hannover':</h1> <h3>Klicke auf den Link, um deine Registrierung abzuschließen: http://localhost/git/registration.php?e=".base64_encode($user)." </h3></div>";
								
								if(sendEmail($mail, "Ihre Registrierung bei TueGutes in Hannover", $mailcontent)===true) {
									echo '<h3><font color=green>Bestätigungslink wurde gesendet an: '.$mail.'</font></h3><p>';
								}
								else {
									echo '<h3><font color=red>Bestätigungslink an '.$mail.' konnte nicht gesendet werden</font></h3><p>';
								}
							} else {
								echo '<font color=red>internal Database error</font><p>';
							}
						}
					} else {//1. Fall: Nutzer ist nicht eingeloggt und gelangt auf Registrierungsseite
						include 'Kontoerstellung.html';
					}
				} else {//4. Fall: Nutzer ist bereits eingeloggt
					echo '<h2>Sie sind bereits eingeloggt</h2>';
					//TODO: Auf Profilseite weiterleiten
				}
	require "./includes/_bottom.php"; 
?>
	