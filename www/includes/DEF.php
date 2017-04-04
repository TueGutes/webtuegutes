<?php
/**
 * Fügt Standardvariablen und Funktionen hinzu
 *
 * Fügt Standardvariablen und Funktionen (wie zum Beispiel die SESSION des Nutzers mit $_USER Objekt und Sprachvariablen) zum Script hinzu
 *
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 */

function get_browser_name($user_agent)
{
	if(strpos($user_agent, 'Chrome'))
		return 'Chrome';
	else if(strpos($user_agent, 'Firefox'))
		return 'Firefox';
	else if(strpos($user_agent, 'Safari'))
		return 'Safari';
	else if(strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/'))
		return 'Opera';
	else if(strpos($user_agent, 'Edge'))
		return 'Edge';
	else if(strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7'))
		return 'Internet Explorer';
	return 'Unknown';
}

//====Server====
$ABSOLUE_PATH = "/";
$HOSTNAME = $_SERVER['HTTP_HOST'];
$HOST_FULL = "http" . (isset($_SERVER['HTTPS']) ? "s" : "") . "://" . $HOSTNAME . $_SERVER['REQUEST_URI'];
$HOST = substr($HOST_FULL, 0, strrpos($HOST_FULL, "/"));

$BROWSER_NAME = get_browser_name($_SERVER['HTTP_USER_AGENT']);

$USE_GMAIL = false; // Bei true wird der gmail Account tuegutesinhannover@gmail.com von der PHPMailer Klasse verwendet um Mails zu senden. Bei false wird die PHP Funktion mail verwendet

//====Datenbank====
//$DB_HOST = "localhost";
//$DB_DATABASE = "tuegutes";
//$DB_USER = "tuegutes";
//$DB_PASSWORD = "password";

//====Config====
$_GROUP_ADMIN = 3;
$_GROUP_MODERATOR = 2;
$_GROUP_USER = 1;

session_start();

include './includes/mail.php';

require './includes/user.php';
$_USER = new User();

require './includes/LANGUAGE.php';
?>