<?php
/*
*@author Henrik Huckauf
*/

//include './includes/ACCESS.php';
include './db/db_connector.php';
require './includes/_top.php';
?>

<h2><?php echo $wlang['deeds_head']; ?> </h2>
<div class='center'>
	<table>
		<?php
		$mysqli = db_connect();
		$result = $mysqli->query("SELECT * FROM deeds");
		// Tabellenkopf mit den Feldnamen als Spaltenbezeichnungen:
		echo " <tr>\n";asdsd
		?>
	</tableasdsad
</div>

<?php
require './includes/_bottom.php';
?>