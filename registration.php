<?php
/*
*@author: Andreas Blech (refactored Henrik Huckauf)
*/

require "./includes/DEF.php";

//Inkludieren von script-Dateien
include './includes/db_connector.php';

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
	$sql = "SELECT idUser FROM User WHERE email = LOWER(?)";
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
	$sql = "Insert into User (username, password, email, regDate, points, status, idUserGroup, idTrust) values(?,?,LOWER(?),?,0,'nichtVerifiziert',1,1)";
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


$username = isset($_POST['benutzername']) ? $_POST['benutzername'] : '';
$vorname = isset($_POST['vorname']) ? $_POST['vorname']: '';
$nachname = isset($_POST['nachname']) ? $_POST['nachname'] : '';
$pass = isset($_POST['passwort']) ? $_POST['passwort'] : '';
$passwdh = isset($_POST['passwortwdh']) ? $_POST['passwortwdh'] : '';
$mail = isset($_POST['mail']) ? $_POST['mail'] : '';

$output = '';
if(isset($_GET['c'])) // man kann auch wenn man mit einem Account angemeldet ist, einen anderen Account verifizieren
{
	if(activateAcount($_GET['c']) === true)
	{
		$goto = $HOST . ($user->loggedIn() ? "" : "/login?code=101");
		header("Location: " . $goto);
	}
	else
	{
		//Das Aktivieren des Accounts hat aus unbekanntem Grund nicht funktioniert
		//Informiere den Benutzer darüber
		$output = '<red>Upps, da ist etwas schief gegangen :(</red>';	
	}
}
else
{
	if(!$user->loggedIn())
	{
		if(isset($_POST['set']) && $_POST['set'] == '1')
		{
			$error = false;
			if(empty($username))
			{
				$output .= "<red>Geben Sie einen Benutzernamen an!</red><br>";
				$error = true;
			}
			else
			{
				// http://regexr.com/
				if(strlen($username) < 3 || strlen($username) > 20 || !preg_match("/^[a-zA-Z0-9 äöü ÄÖÜ ß]+([_.\-]?[a-zA-Z0-9 äöü ÄÖÜ ß])*$/", $username)) ///^[a-zA-Z0-9 äöü ÄÖÜ ß]+([_.\s\-]?[a-zA-Z0-9 äöü ÄÖÜ ß])*$/
				{
					$output .= "<red>Für den Benutzernamen gilt:<br>- der Benutzername muss zwischen 3 und 20 Zeichen lang sein<br>- der Benutzername darf nicht mit Sonderzeichen beginnen oder enden<br>- es dürfen nicht mehrere Sonderzeichen aufeinander folgen<br>- der Benutzername darf nur aus a-z A-Z 0-9 äöü ÄÖÜ ß _ . und LEERZEICHEN bestehen</red><br>";
					$error = true;
				}
			}
			if(idOfBenutzername($username) != false)
			{
				$output .= "<red>Der gewählte Benutzername ist bereits registriert!</red><br>";
				$error = true;
			}			
			if(empty($vorname))
			{
				$output .= "<red>Geben Sie ihren Vornamen an!</red><br>";
				$error = true;
			}
			if(empty($nachname))
			{
				$output .= "<red>Geben Sie ihren Nachnamen an!</red><br>";
				$error = true;
			}
			if(empty($pass))
			{
				$output .= "<red>Geben Sie ein Passwort an!</red><br>";
				$error = true;
			}
			else if($pass !== $passwdh)
			{
				$output .= "<red>Die Passwörter stimmen nicht überein!</red><br>";
				$error = true;
			}
			
			if(!$error)
			{
				$cryptkey = createBenutzerAccount($username, $vorname, $nachname, $mail, $pass);
				if($cryptkey)
				{
					//Account erfolgreich in Datenbank erstellt
					//Sende Bestätigungslink an Mailadresse
					$actual_link = $HOST . "/registration";
					
					$mailcontent = "<div style=\"margin-left:10%;margin-right:10%;background-color:#757575\"><img src=\"img/wLogo.png\" alt=\"TueGutes\" title=\"TueGutes\" style=\"width:25%\"/></div><div style=\"margin-left:10%;margin-right:10%\"><h1>Herzlich Willkommen <b>".$vorname."</b> bei 'Tue Gutes in Hannover':</h1> <h3>Klicke auf den Link, um deine Registrierung abzuschließen: ".$actual_link."?c=".$cryptkey." </h3></div>";

					if(sendEmail($mail, "Ihre Registrierung bei TueGutes in Hannover", $mailcontent) === true)
						header("Location: " . $HOST. "/login?code=102");
					else
						$output = '<red>Bestätigungslink an ' . $mail . ' konnte nicht gesendet werden!</red>';
				}
				else
					$output = '<red>Interner Fehler:<br>Es konnte kein Benutzeraccount angelegt werden!</red>';
			}
		}
	}
	else
		header("Location: " . $HOST);
}
				
require "./includes/_top.php";
?>				
	
<h2><?php echo $wlang['register_head']; ?></h2>

<div id='output'><?php echo $output; ?></div>
<br><br>
<div class="center">	
	<form action="" method="post">
		<input type="text" name="benutzername" value="<?php echo $username; ?>" placeholder="<?php echo "Benutzername"; ?>" required /><br>
		<input type="text" name="vorname" value="<?php echo $vorname; ?>" placeholder="<?php echo "Vorname"; ?>" required /><br>
		<input type="text" name="nachname" value="<?php echo $nachname; ?>" placeholder="<?php echo "Nachname"; ?>" required /><br>
		<input type="password" name="passwort" value="" placeholder="<?php echo "Passwort"; ?>" required /><br>
		<input type="password" name="passwortwdh" value="" placeholder="<?php echo "Passwort wiederholen"; ?>" required /><br>
		<input type="text" name="mail" value="<?php echo $mail; ?>" placeholder="<?php echo "E-Mail Adresse"; ?>" required /><br>
		<br>
		<input type='hidden' name='set' value='1' />
		<input type="submit" value="Registrieren" />
	</form>
	<br><br>
	<a href="./PasswortUpdate">Passwort vergessen?</a>
	<br><br>
	<a href="./login">Bereits registriert?</a>
</div>

<?php	
require "./includes/_bottom.php"; 
?>
	