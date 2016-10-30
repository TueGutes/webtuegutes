<?php
	/*
	*@author Nick Nolting
	*/
	
	session_start();

	//Prüfung, ob der Nutzer sich bereits eingeloggt hat.
	if (!(isset($_SESSION['user']))) {
		$_SESSION['user'] = "";
	}
?>