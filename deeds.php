<?php
/*
*@author Lukas Buttke
*/

//include './includes/ACCESS.php';

session_start();

if (!@$_SESSION['loggedIn']) die (Header("Location: ./login.php")); 	


include './includes/db_connector.php';
require './includes/_top.php';
?>

/*<html>
<head> <title> Gute Taten anzeigen </title> 

</head>

<body>*/ 
<h2><?php echo $wlang['deeds_head']; ?> </h2>

<!-- Hier kann später mal gute Taten erstellen hervorkommen-->
<div class='ctop'>
<input type="button" value="Gute Tat erstellen" onclick="window.open('./guteTatErstellenHTML.php','Deed')">
<br> <hr>
</div>

<div class='center'>
	<table>
		<?php
		$mysqli = db_connect();
		$result = $mysqli->query("SELECT * FROM deeds");
		// Tabellenkopf mit den Feldnamen als Spaltenbezeichnungen:
		echo " <tr> <th> ";
		while ( $field = $result->fetch_field() ) {
			//if(($field->name !== "idGuteTat")&&($field->name !== "contactPerson")&&($field->name !== "status")){
				echo " $field->name";
			//}
		}
		echo " </th> </tr>";		 
		 
		 // Tabelleneinträge aus der Datenbank
		while ( $deed = $result->fetch_object() ) {

			echo " <tr> <td> ";
			foreach ( $deed as $key => $value ) 
			{
				echo "$value";	
			}
			echo " </td> </tr>";
		}
		?>
	</table>
</div>

</body>
</html>

<?php
require './includes/_bottom.php';
?>