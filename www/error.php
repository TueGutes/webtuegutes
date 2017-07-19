<?php
/**
 * Error handling
 *
 * Regelt, wie mit auftretenden Errors umgegangen wird 
 *
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 */

$errorCode = isset($_REQUEST['e']) ? $_REQUEST['e'] : $_SERVER["REDIRECT_STATUS"];
if($errorCode == 200) // sollte man die error Seite manuell besuchen... (Status Code 200 -> OK)
{
	header('Location: ./');
	exit;
}
	
if($errorCode == 403) // fake 404 wenn der Zugriff verweigert wird (403) aus Sicherheitsgründen
	$errorCode = 404;

require './includes/DEF.php';
require './includes/_top.php';
?>

<?php 
	echo '<h2>Error ' . $errorCode . '</h2>'; 
	
	switch($errorCode)
	{
		case 403:
		case 404:
		case 500:
			echo $wlang['error'][$errorCode];
			break;
		default:
			echo $wlang['error']['default'];			
	}

?>

<?php
require './includes/_bottom.php';
?>