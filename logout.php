<?php
	session_start();

	//Ausloggen des Nutzers
	session_destroy();

	//Neuladen der aktuellen Seite
	echo "<script type='text/javascript'>window.parent.location.reload()</script>";
?>