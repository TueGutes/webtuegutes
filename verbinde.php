
<?php

DEFINE('DB_USER','root');
DEFINE('DB_PASSWORD','');
DEFINE('DB_HOST','localhost');
DEFINE('DB_NAME','tuegutesdb');
$db = mysqli_connect(DB_HOST,DB_USER, DB_PASSWORD, DB_NAME);
if(!$db)
{
  exit("Verbindungsfehler: ".mysqli_connect_error());
}
else{
	echo'Verbindung erfolgreich\r\n';
}

$benutzername = $_POST['benutzername'];
$passwort = $_POST['passwort'];
$mail_php = $_POST['mail'];
#$users_comment = $_POST['comment'];
$datum = CURDATE();
$stmt_text = "INSERT INTO Benutzer (Benutzername, Email, Passwort, RegDatum) values('$benutzername','" . sha1($passwort.$date) . ") ','" . "$mail_php" . "'," . $datum . ")";
#echo $stmt_text;

$stmt = mysqli_prepare($db,$stmt_text) or die(mysqli_error($db));
mysqli_stmt_execute($stmt) or die(mysqli_error($db));



#$response = mysqli_execute(stmt);
?>
