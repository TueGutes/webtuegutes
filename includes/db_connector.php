<?php 

//Definition der Datenbankverbindung
DEFINE('DB_USER','root');
DEFINE('DB_PASSWORD','');
DEFINE('DB_HOST','localhost');
DEFINE('DB_NAME','mydb');

function db_connect() {
	return mysqli_connect(DB_HOST,DB_USER, DB_PASSWORD, DB_NAME);
}

function db_close(mysqli $db) {
	mysqli_close($db);
}

?>