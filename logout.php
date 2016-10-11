<?php
	session_start();
	session_destroy();
	echo 'Sie wurden erfolgreich ausgeloggt!<p><a href=../'.$_GET['source'].'>Zurück</a>';
?>