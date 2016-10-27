<?php

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

				/*if(isset($_SESSION['loggedIn'])===true) {
						if($_SESSION['loggedIn'] === true) {
							echo 'true'.$_SESSION['loggedIn'];
						}
						else {
							echo 'false';
							
						}
				}
				else {
					echo 'tschüss';
				}*/
				
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
									<input type="submit" value="Email senden">
									</center>
								</form>';
								//TODO: Man muss auch den Cryptkey mit übergeben...
						
					} elseif(isset($_POST['mail'])) { //2.Fall: Zeige Meldung und schicke Bestätigungslink
						echo '<h2>Passwort Ändern</h2>';
						echo '<h3>Email gesendet an: '.$_POST['mail'].'</h3>';
						echo '<h4>Klicken sie auf die URL in der Email, um das Passwort zu ändern</h4>';
						//TODO: Cryptkey aus Datenbank anhand der Mail laden
						//TODO: Email mit Bestätigungslink und Cryptkey senden
						
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
									<input type="submit" value="Email senden">
									</center>
								</form>';
								//TODO: Man muss auch den Cryptkey mit übergeben...
						}
						else {
							//TODO: Passwort wirklich ändern :)
							echo'<h3>Passwort erfolgreich geändert</h3>';
						}
						//$_SESSION['loggedIn']=true;
						//Header("Location: ./");
					} else{ //1. Fall: Nutzer eingeloggt und auf PasswortUpdate.php gelangt 
						echo'<form action="PasswortUpdate.php" method="post">
									<center>
										<h2>Passwort Ändern</h2>
	
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
					//Automatisch zur Startseite weiterleiten
					echo '<h3>Du bist bereits eingeloggt</h3>';
					//Header("Location: ./");
				}

				/*if(isset($_GET['c']) && isset($_GET['p'])) {
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
						
						$mailcontent = "<div style=\"margin-left:10%;margin-right:10%;background-color:#757575\"><img src=\"img/logo_provisorisch.png\" alt=\"Zurück zur Startseite\" title=\"Zurück zur Startseite\" style=\"width:25%\"/></div><div style=\"margin-left:10%;margin-right:10%\"><h1>Herzlich Willkommen <b>".$vorname."</b> bei 'Tue Gutes in Hannover':</h1> <h3>Klicke auf den Link, um deine Registrierung abzuschließen: http://localhost/git/registration.php?e=".base64_encode($user)." </h3></div>";
						sendEmail($mail, "Ihre Registrierung bei TueGutes in Hannover", $mailcontent);
								
					}
				}
				elseif($_SESSION['loggedIn']===false) {
					//Nutzer ist nicht eingeloggt und hat das Formular noch nicht abgeschickt
					include 'passwortAnfordern.html';
				}
				else {
					//Der Nutzer ist bereits eingeloggt. Das "Passwort Vergessen" Menü ergibt keinen Sinn
					//Leite weiter auf Startseite, vielleicht lieber persönliches Profil
					header('Location: ./');
				}*/
	require "./includes/_bottom.php"; 				
?>