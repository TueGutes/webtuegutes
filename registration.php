<?php
//Author: Andreas Blech
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
	$db = db_connect();
	$sql = "SELECT idUser FROM User WHERE username = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('s',$benutzername);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);
	if(isset($dbentry['idUser'])){
		return $dbentry['idUser'];
	}
	else {
		return false;
	}
	
	//return false; //Testzwecke
}

//Gibt das Attribut idBenutzer zu einer gegebenen email Adresse zurück oder false, falls
//es keinen Account mit dieser Emailadresse gibt
function idOfEmailAdresse($emailadresse) {
	$db = db_connect();
	$sql = "SELECT idUser FROM User WHERE email = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('s',$emailadresse);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);				
	if(isset($dbentry['idUser'])){
		return $dbentry['idUser'];
	}
	else {
		return false;
	}
	
	//return false; //Testzwecke
}

/*Erstellt einen Benutzeraccount mit den angegeben Parametern, der Status ist erste einmal "unverifiziert*/
/*Liefert einen cryptkey, falls das Erstellen erfolgreich war, false falls nicht*/
function createBenutzerAccount($benutzername, $vorname, $nachname, $email, $passwort) {
	//TODO: Datenbank Insert ausarbeiten
	$db = db_connect();
	$sql = "Insert into User (username, password, email, regDate, points, status, idUserGroup, idTrust) values(?,?,?,?,0,'nichtVerifiziert',0,0)";
	$stmt = $db->prepare($sql);
	$date = date("Y-m-d");
	$pass_md5 = md5($passwort.$date);
	mysqli_stmt_bind_param($stmt, "ssss", $benutzername, $pass_md5, $email,$date);
	$stmt->execute();
	$affected_rows = mysqli_stmt_affected_rows($stmt);
	if($affected_rows == 1) {
		//return true;	
	} else {
		echo 'beim erstellen des nutzers ist was schief gegangen '.mysqli_error($db);
		//return false;
	}
	
	$sql = "Insert into Privacy (idPrivacy, privacykey, cryptkey) values ((SELECT MAX(idUser) FROM User),?,?)";
	$stmt = $db->prepare($sql);
	
	$cryptkey = md5($benutzername.$date); //Der Cryptkey wird erstellt
	$privacykey = "111111111111111";
	mysqli_stmt_bind_param($stmt, "ss", $privacykey, $cryptkey);
	$stmt->execute();
	$affected_rows = mysqli_stmt_affected_rows($stmt);
	if($affected_rows == 1) {
		//return true;	
	}
	else {
		echo 'beim erstellen des privacys ist was schief gegangen: '.mysqli_error($db);
		return false;
	}

	$sql = "Insert into UserTexts (idUserTexts) values ((SELECT MAX(idUser) FROM User))";
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$affected_rows = mysqli_stmt_affected_rows($stmt);
	if($affected_rows == 1) {
		//return true;	
	}
	else {
		echo 'beim erstellen des privacys ist was schief gegangen: '.mysqli_error($db);
		return false;
	}
	
	$sql = "Insert into PersData (idPersData, firstname, lastname) values((SELECT MAX(idUser) FROM User),?,?)";
	$stmt = $db->prepare($sql);
	mysqli_stmt_bind_param($stmt, "ss", $vorname, $nachname);
	$stmt->execute();
	$affected_rows = mysqli_stmt_affected_rows($stmt);
	if($affected_rows == 1) {
		//return true;	
	}
	else {
		echo 'beim erstellen von PersData Eintrag ist was schief gegangen '.mysqli_error($db);
		return false;
	}
	
	db_close($db);	
	
	return $cryptkey;
	
	//return "asdfjklö"; //Für Testzwecke
}

/*Setzt den Status des zum cryptkey gehörenden Accounts auf "verifiziert"*/
function activateAcount($cryptkey) {
	$db = db_connect();
	$sql = "UPDATE User SET status = 'Verifiziert' WHERE idUser = (SELECT idPrivacy FROM Privacy WHERE cryptkey = ?)";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('s',$cryptkey);
	$stmt->execute();
	//$result = $stmt->get_result();
	//$dbentry = $result->fetch_assoc();
	db_close($db);				
	//if(isset($dbentry['idUser'])){
	//	return $dbentry['idUser'];
	//}
	//else {
	//	return false;
	//}
	//Verfiziert
	return true;
}

/*Liefert den Benutzernamen des Accounts, der zum cryptkey gehört oder false*/
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
			
				$_SESSION['loggedIn'] = false; //Testzwecke
			
				if($_SESSION['loggedIn'] === false) {
					if(isset($_GET['c'])) {//3. Fall: Bestätigungslink
						if(activateAcount($_GET['c']) === true) {
							$_SESSION['loggedIn'] = true;
							$_SESSION['user'] = getUserByCryptkey($_GET['c']); 
							header("Location: http://localhost/git/");
							//TODO auf Profilseite weiterleiten
						} else {
							//Das Aktivieren des Accounts hat aus unbekanntem Grund nicht funktioniert
							//Informiere den Benutzer darüber
							echo '<h3><font color=red>Upps, da ist etwas schief gegangen :(</font></h3><p>';
							
						}
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
							echo '<h3><font color=red>Fehler! Bitte alle Felder ausfüllen!</font></h3><p>';
							include './includes/Kontoerstellung.html';
						} elseif (idOfBenutzername($user) != false){
							//2.2 Fall: Benutzername existiert bereits
							echo '<h3><font color=red>Fehler! Benutzername bereits vergeben</font></h3><p>';
							include './includes/Kontoerstellung.html';
						} elseif ($pass != $passwdh) { 
							//2.3 Fall: Passwörter sind nicht identisch
							echo '<h3><font color=red> Fehler! Passwörter stimmen nicht überein</font></h3><p>';
							include './includes/Kontoerstellung.html';
						} elseif(idOfEmailAdresse($mail) != false) { //Email-Adresse bereits registriert
							echo '<h3><font color=red> Fehler! Email-Adresse bereits registriert</font></h3><p>';
							include './includes/Kontoerstellung.html';
							echo'<a href="PasswortUpdate.php">Passwort vergessen?</a>';
						} else {//Alles okay, erstelle neuen Account und sende Bestätigungsmail
							$cryptkey = createBenutzerAccount($user, $vorname, $nachname, $mail, $pass);
							if($cryptkey != false) {
								//Account erfolgreich in Datenbank erstellt
								//Sende Bestätigungslink an Mailadresse
								
								$mailcontent = "<div style=\"margin-left:10%;margin-right:10%;background-color:#757575\"><img src=\"img/wLogo.png\" alt=\"TueGutes\" title=\"TueGutes\" style=\"width:25%\"/></div><div style=\"margin-left:10%;margin-right:10%\"><h1>Herzlich Willkommen <b>".$vorname."</b> bei 'Tue Gutes in Hannover':</h1> <h3>Klicke auf den Link, um deine Registrierung abzuschließen: http://localhost/git/registration.php?c=".$cryptkey." </h3></div>";
								
								if(sendEmail($mail, "Ihre Registrierung bei TueGutes in Hannover", $mailcontent)===true) {
									echo '<h3><font color=green>Bestätigungslink wurde gesendet an: '.$mail.'</font></h3><p>';
								}
								else {
									//Das Senden der Email ist fehlgeschlagen
									echo '<h3><font color=red>Bestätigungslink an $mail konnte nicht gesendet werden</font></h3><p>';
								}
							} else {
								//Das Erstellen des Accounts in der Datenbank ist schief gelaufen
								echo '<h3><font color=red>Interner Fehler: Es konnten kein Benutzeraccount angelegt werden</font></h3><p>';
							}
						}
					} else {//1. Fall: Nutzer ist nicht eingeloggt und gelangt auf Registrierungsseite
						echo'<h2>Registrierung</h2>';
						echo'<h3>Trage deine Daten ein, um dich zu registrieren</h3>';
						include './includes/Kontoerstellung.html';
					}
				} else {//4. Fall: Nutzer ist bereits eingeloggt
					echo '<h2>Sie sind bereits eingeloggt</h2>';
					//TODO: Auf Profilseite weiterleiten
				}
	require "./includes/_bottom.php"; 
?>
	