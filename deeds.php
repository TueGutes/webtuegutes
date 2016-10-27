<?php
/*
*@author Lukas Buttke
*/

//include './includes/ACCESS.php';

include './db/db_connector.php';
require './includes/_top.php';
?>

<html>
<head> <title> Gute Taten anzeigen </title> 

</head>

<body> 
<h2><?php echo $wlang['deeds_head']; ?> </h2>

<div class='center'>
	<table>
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
</div>

</body>
</html>

<?php
require './includes/_bottom.php';
?>