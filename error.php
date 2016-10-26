<?php
/*
*@author Henrik Huckauf
*/

$errorCode = $_SERVER["REDIRECT_STATUS"];
if($errorCode == 200) // sollte man die error Seite manuell besuchen... (Status Code 200 -> OK)
	header('Location: ./');

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