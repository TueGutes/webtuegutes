<?php
/*
*@author Klaus Sobotta, Lukas Buttke
*/

include './includes/ACCESS.php';
include './includes/db_connector.php';
require './includes/_top.php';
//require_once './guteTatAusgeben.php';

if (!isset($_GET['page']) /* || db_getGuteTatenAnzahl() >=  */) $_GET['page'] = 1;

?>

<h2><?php echo $wlang['deeds_head']; ?> </h2>

<!-- Hier kann später mal gute Taten erstellen hervorkommen-->
<div class='ctop'>
<form action="guteTatErstellenHTML.php" method="post">
<input type="submit" value="Gute Tat erstellen" target="_self">
<br> <hr>
</div>
<br> 

		<?php
		
			/*$intZahl=0;
			$tatAusgeben=new tatAusgeben($intZahl);
			$tatAusgeben->toStringTat();
			*/
			//$allDeedsCount = db_connector blabla
			//$neededPages = $allDeedsCount/10;
			
			$arr = db_getGuteTatenForList(10*($_GET['page']-1), 10);
			$maxZeichenFürDieKurzbeschreibung = 150;

			for($i = 0; $i < sizeof($arr); $i++){
				echo "<a href='./deeds_details?id=" . $arr[$i]->idGuteTat . "' style='display: inline-block; width: 80%;'><div class='deed" . ($arr[$i]->status == "geschlossen" ? " closed" : "") . "' style='width: 100%; background: #aaaaaa; overflow: hidden;'>";
					echo "<div style='position: realtive; float: left;'><h4>" . $arr[$i]->name . "</h4></div><div style='position: realtive; float: right;'>" . $arr[$i]->category . "</div>";
					echo "<br><br><br><br><div style='position: realtive; text-align: left;'>" . (strlen($arr[$i]->description) > $maxZeichenFürDieKurzbeschreibung ? substr($arr[$i]->description, 0, $maxZeichenFürDieKurzbeschreibung) . "...<br>mehr" : $arr[$i]->description) . "</div>";
					echo "<div style='position: realtive; float: left;'>" . $arr[$i]->street ." ,  ". $arr[$i]->housenumber ." ,  ". $arr[$i]->postalcode . "</div>";
					echo "<div>" . (is_numeric($arr[$i]->countHelper)?"Anzahl der Helfer: ". $arr[$i]->countHelper:'') ."</div><div style='position: realtive; float: right;'>"."Das Mindest Vertraunslevel betraegt ". $arr[$i]->idTrust . "</div>";
					echo "<div>" . $arr[$i]->organization . "</div>";
				echo "</div></a>";
				echo "<br><br><hr><br><br>";
			}
			
		?>

<br> <hr>	

</form>

<!--Zurück / Weiter Buttons-->
<?php
	$vor = $_GET['page'] > 1;
	$nach = db_getGuteTatenForList(10*($_GET['page']), 10); //Sobald Deeds nicht mehr doppelt ausgegeben werden -> Mit Anzahl prüfen!
	if ($vor) echo '<a href="./deeds?page=' . ($_GET['page']-1) . '"><input type="button" value="Zurück"></a>';
	if ($vor && $nach) echo '&nbsp';
	if ($nach) echo '<a href="./deeds?page=' . ($_GET['page']+1) . '"><input type="button" value="Weiter"></a>';
?>

<br> 


<?php
require './includes/_bottom.php';
?>
