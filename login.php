<?php
/*
*@author Henrik Huckauf, Andreas Blech
*/
session_start();

//Prüfung, ob der Nutzer sich bereits eingeloggt hat.
if(!(isset($_SESSION['loggedIn']))) {
	$_SESSION['loggedIn'] = false;
}
include './includes/db_connector.php';

require './includes/_top.php';
require_once './includes/LANGUAGE.php';

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

/*Liefert das Registrierungsdatum zu einer UserID oder false*/
function regDateOfUserID($userID) {
	$db = db_connect();
	$sql = "SELECT regDate FROM User WHERE idUser = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$userID);
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

/*Liefert den PasswortHash zu einer UserID oder false*/
function passwordHashOfUserID($userID) {
	$db = db_connect();
	$sql = "SELECT password FROM User WHERE idUser = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$userID);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	if(isset($dbentry['password'])){
		db_close($db);
		return $dbentry['password'];
	}
	else {
		echo "Error: ".mysqli_error($db);
		db_close($db);
		return false;
	}
}

if(isset($_POST['username']) && isset($_POST['password'])) {
	/*Login-Formular mit Username und Passwort wurde aufgerufen
	Überprüfe, ob Username in Tabelle existiert
	Überprüfe, ob Passwort richtig ist, falls ja melde Benutzer an und leite auf Profilseite weiter
	*/
	$continue = ""; //TODO Profilseite
	if(isset($_SESSION['continue'])) {
		$continue = $_SESSION['continue'];
		unset($_SESSION['continue']);	
	}
	
	$userID = idOfBenutzername($_POST['username']);
	if($userID != false) {
		$regDate = regDateOfUserID($userID);
		$passHash = passwordHashOfUserID($userID);
		if(md5($_POST['password'].$regDate) === $passHash) {
			//Eingegebenes Passwort ist richtig
			$_SESSION['loggedIn'] = true;
			$_SESSION['user'] = $_POST['username']; 
			echo '<h3>Login erfolgreich</h3>';
			header("Location: ".$continue); //Weiterleiten auf URL in $continue
		}
		else {
			//Eingebenes Passwort ist falsch
			$head = $wlang['login_head'];
			echo '<h2>'.$head.'</h2>';
			echo '<h3><font color=red>Das eingegebene Passwort ist falsch</font></h3>';
			require 'loginFormular.php';
			echo'<a href="PasswortUpdate.php">Passwort vergessen?</a>';
		}
	}
	else {
		$head = $wlang['login_head'];
		echo '<h2>'.$head.'</h2>';
		echo '<h3><font color=red>Der eingegebene Benutzername ist uns nicht bekannt</font></h3>';
		require 'loginFormular.php';
	}
	
} else {
	$_SESSION['loggedIn'] = false; //Standardmäßig wird man ausgeloggt, wenn man auf die Loginseite kommt (klickt man auf Logout wird login.php aufgerufen, sollte man vielleicht schöner machen...)
	//Der Continue Parameter muss mit übergeben werden
	if(isset($_GET['continue'])) {
		$_SESSION['continue'] = $_GET['continue'];
	}
	
	$head = $wlang['login_head'];
	echo '<h2>'.$head.'</h2>';

	require 'loginFormular.php';

}

require './includes/_bottom.php';
?>
