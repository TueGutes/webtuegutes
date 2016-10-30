<?php
/*
*@author Lukas Buttke
*/

//include './includes/ACCESS.php';

session_start();

include './db/db_connector.php';
require './includes/_top.php';
?>

/*<html>
<head> <title> Gute Taten anzeigen </title> 
<script>
var nextPage = function(){
	document.getElementById("out").innerHTML = "Button gedruckt";
}

</script> 
</head>

<body>*/ 
<h2><?php echo $wlang['deeds_head']; ?> </h2>

<!-- Hier kann später mal gute Taten erstellen hervorkommen-->
<div class='ctop'>
<input type="button" value="Gute Tat erstellen" onclick="window.open('./db/erstelleDeed.php','Deed')">
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
			if(($field->name !== "idGuteTat")&&($field->name !== "contactPerson")&&($field->name !== "status")){
				echo " $field->name";
			}
		}
		echo " </th> </tr>";
// 		Versuch einen Button mit in die Tabelle zu stellen misglückt.
//		<input type="button" value="weiterlesen" onclick="nextPage();"> 
//		<p id="out"> yo </p>
		 
		 
		 // Tabelleneinträge aus der Datenbank
		while ( $deed = $result->fetch_object() ) {

			echo " <tr> <td> ";
			foreach ( $deed as $key => $value ) 
			{
				echo "out $value";	
			}
			echo " </td> </tr>";
		}
		?>
	</table>
</div>

/*</body>
</html>
*/
<?php
require './includes/_bottom.php';
?>