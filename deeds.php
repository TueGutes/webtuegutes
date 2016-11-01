<?php
/*
*@author Lukas Buttke
*/

include './includes/ACCESS.php';
include './includes/db_connector.php';
require './includes/_top.php';
?>

<h2><?php echo $wlang['deeds_head']; ?> </h2>

<!-- Hier kann später mal gute Taten erstellen hervorkommen-->
<div class='ctop'>
<form action="guteTatErstellenHTML.php">
<input type="submit" value="Gute Tat erstellen" target="_self">
<br> <hr>
</div>

<div class='center'>
	<table style="display: inline-block;border:1px solid">
		<?php
		$mysqli = db_connect();
		$result = $mysqli->query('SELECT name AS "Gute Tat", username AS "Kontakt", street AS "Straße", housenumber AS "Nr.", Postalcode.postalcode AS "PLZ", place AS "Ort", description AS "Beschreibung:" FROM Deeds JOIN Postalcode ON (Deeds.postalcode = Postalcode.postalcode) JOIN DeedTexts ON (Deeds.idGuteTat = DeedTexts.idDeedTexts) JOIN User ON (Deeds.contactPerson = User.idUser)');
		// Tabellenkopf mit den Feldnamen als Spaltenbezeichnungen:
		echo ' <tr>';
		while ( $field = $result->fetch_field() ) {
			echo '<th style="border:1px solid;padding:10px"><b>';
			//if(($field->name !== "Beschreibung")){
			echo " $field->name";
			echo '</b></th>';
		}
		echo "</tr>";		 
		 
		 // Tabelleneinträge aus der Datenbank
		while ( $deed = $result->fetch_object() ) {

			echo ' <tr> ';
			foreach ( $deed as $key => $value ) 
			{
				echo '<td style="border:0px solid;padding:10px">';
				echo "$value";	
				echo '</td>';			
			}

			echo '<td style="border:0px solid;padding:10px">';
			echo '<a href="./guteTat.php"><input type="button" value="mehr"/></a>';
			echo '</td>';
			echo " </tr>";
		}
		?>
	</table>
</div>

</body>
</html>

<?php
require './includes/_bottom.php';
?>