<?php
/**
 * Bietet die Möglichkeit sich im System anzumelden
 * Ein Nutzer gibt seinen Benutzernamen und sein Passwort ein
 * Sind alle Daten korrekt wird er eingeloggt und anschließend standardmäßig zu seiner Profilseite (profile.php) weiterleitet
 * Wollte der Nutzer ursprünglich zu einer anderen Seite, war allerdings noch nicht eingeloggt, so wird er nach dem Login dorthin weitergeleitet (continue Parameter)
 * (refactored von Henrik Huckauf)
 *
 * @author Andreas Blech <andreas.blech@stud.hs-hannover.de>
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 */

require './includes/DEF.php';

if($_USER->loggedIn())
	$_USER->redirect($HOST);

include './includes/db_connector.php';

//DB Funktionen, die später ausgelagert werden sollten
// TIMM:
// ausgelagert in db_connector, code ist gesaved in einer .txt bei mir


$username = isset($_POST['username']) ? $_POST['username'] : '';
$output = isset($_GET['code']) ? (isset($wlang['login_code_' . $_GET['code']]) ? $wlang['login_code_' . $_GET['code']] : "") : "";
if(isset($_POST['username']) && isset($_POST['password']))
{
	$continue = $HOST . "/profile";
	if(isset($_POST['continue']) && $_POST['continue'] != '' && parse_url($_POST['continue'])['host'] == parse_url($HOST)['host'])
		$continue = urldecode($_POST['continue']);

	$userID = DBFunctions::db_idOfBenutzername($_POST['username']);
	if($userID != false)
	{
		$regDate = DBFunctions::db_regDateOfUserID($userID);
		$passHash = DBFunctions::db_passwordHashOfUserID($userID);
		$status = DBFunctions::db_statusByUserID($userID);
		if($status != "nichtVerifiziert")
		{
			if(md5($_POST['password'].$regDate) === $passHash) //Eingegebenes Passwort ist richtig
			{
				$username = $_POST['username'];
				$dbentry = DBFunctions::db_get_user($username);
				$_USER->login($userID, $username, $dbentry['email']);
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
	<input type="text" value="<?php echo $username; ?>" name="username" placeholder="<?php echo $wlang['login_placeholder_username']; ?>" required autofocus />
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
