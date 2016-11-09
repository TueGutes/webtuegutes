<?php
/*
*@author Henrik Huckauf, Andreas Blech
*/

require './includes/DEF.php';

if($_USER->loggedIn())
	$_USER->redirect($HOST);

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

/**/
function statusByUserID($userID) {
	$db = db_connect();
	$sql = "SELECT status FROM User WHERE idUser = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$userID);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	if(isset($dbentry['status'])){
		db_close($db);
		return $dbentry['status'];
	}
	else {
		echo "Error: ".mysqli_error($db);
		db_close($db);
		return false;
	}
}


$output = isset($_GET['code']) ? (isset($wlang['login_code_' . $_GET['code']]) ? $wlang['login_code_' . $_GET['code']] : "") : "";
if(isset($_POST['username']) && isset($_POST['password']))
{
	$continue = $HOST . "/profile";
	if(isset($_POST['continue']) && $_POST['continue'] != '')
		$continue = urldecode($_POST['continue']);
	
	$userID = idOfBenutzername($_POST['username']);
	if($userID != false)
	{
		$regDate = regDateOfUserID($userID);
		$passHash = passwordHashOfUserID($userID);
		$status = statusByUserID($userID);
		if($status != "nichtVerifiziert")
		{
			if(md5($_POST['password'].$regDate) === $passHash) //Eingegebenes Passwort ist richtig
			{
				$username = $_POST['username'];
				$_USER->login($userID, $username, db_get_user($username)['email']);
				$_USER->redirect($continue); //Weiterleiten auf URL in $continue
				exit;
			}
			else
				$output = '<red>Das eingegebene Passwort ist falsch!</red>';
		}
		else
			$output = '<red>Der Account ist noch nicht verifiziert, bitte auf den Bestätigungslink in der Email klicken!</red>';
	}
	else
		$output = '<red>Der eingegebene Benutzername ist uns nicht bekannt!</red>';	
}


require './includes/_top.php';
?>

<h2><?php echo $wlang['login_head']; ?></h2>

<div id='output'><?php echo $output; ?></div>
<br><br>
<form action="" method="post">
	<input type="text" value="" name="username" placeholder="<?php echo $wlang['login_placeholder_username']; ?>" required />
	<br><br>
	<input type="password" name="password" value="" placeholder="<?php echo $wlang['login_placeholder_password']; ?>" required />
	<br><br>
	<input type="submit" value="<?php echo $wlang['login_button_submit']; ?>" />
	<input type="hidden" name="continue" value="<?php echo isset($_GET['continue'])?$_GET['continue']:''; ?>" />
</form>
<br><br>
<a href="./PasswortUpdate">Passwort vergessen?</a>
<br><br>
<a href="./registration">Noch keinen Account?</a>

<?php
require './includes/_bottom.php';
?>
