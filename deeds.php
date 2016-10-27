<?php
/*
*@author Lukas Buttke
*/

//include './includes/ACCESS.php';

require './includes/_top.php';


?>

<!-- Adressen aus DB. Dateiname: adresseDB1.php -->
<?php 

//Definition der Datenbankverbindung
DEFINE('DB_USER','root');
DEFINE('DB_PASSWORD','');
DEFINE('DB_HOST','localhost');
DEFINE('DB_NAME','tuegutesdb');

function db_connect() {
	return mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
}

function db_close(mysqli $db) {
	mysqli_close($db);
}

?>
<h2>Gute Taten</h2>
<table border="3">
<?php
$mysqli = db_connect();
$result = $mysqli->query("SELECT * FROM deeds");
// Tabellenkopf mit den Feldnamen als Spaltenbezeichnungen:
echo " <tr>\n";
while ( $field = $result->fetch_field() ) {
	if(($field->name !== "idGuteTat")&&($field->name !== "contactPerson")&&($field->name !== "status")){
		echo " <th>$field->name</th>\n";
	}
}
echo " </tr>\n";
// Tabelleneinträge aus der Datenbank
while ( $deed = $result->fetch_object() ) {

	echo " <tr>\n";
	foreach ( $deed as $key => $value ) 
	{
			if($key !== "idGuteTat"){
				echo " <td>$value</td> \n";
			}	
	}
	echo " </tr>\n";
}
?>
</table>

<?php
require './includes/_bottom.php';
?>