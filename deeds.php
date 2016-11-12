<?php
/*
*@author Klaus Sobotta, Lukas Buttke
*/

require './includes/DEF.php';

include './includes/ACCESS.php';

include './includes/db_connector.php';

require './includes/_top.php';

if(!isset($_GET['page']) /* || db_getGuteTatenAnzahl() >=  */) $_GET['page'] = 1;
?>

<h2><?php echo $wlang['deeds_head']; ?> </h2>

<div>
<form action="guteTatErstellenHTML.php" method="post">
<input type="submit" value="Gute Tat erstellen" target="_self">
<br><hr>
</div>
<br> 
		<?php
			$placeholder = 'alle';
			/*$intZahl=0;
			$tatAusgeben=new tatAusgeben($intZahl);
			$tatAusgeben->toStringTat();
			*/
			//$allDeedsCount = db_connector blabla
			//$neededPages = $allDeedsCount/10;
			
			$arr = db_getGuteTatenForList(10*($_GET['page']-1), 10,$placeholder);
			$maxZeichenFürDieKurzbeschreibung = 150;

			for($i = 0; $i < sizeof($arr); $i++){
				echo "<a href='./deeds_details?id=" . $arr[$i]->idGuteTat . "' class='deedAnchor'><div class='deed" . ($arr[$i]->status == "geschlossen" ? " closed" : "") . "'>";
					echo "<div class='name'><h4>" . $arr[$i]->name . "</h4></div><div class='category'>" . $arr[$i]->category . "</div>";
					echo "<br><br><br><br><div class='description'>" . (strlen($arr[$i]->description) > $maxZeichenFürDieKurzbeschreibung ? substr($arr[$i]->description, 0, $maxZeichenFürDieKurzbeschreibung) . "...<br>mehr" : $arr[$i]->description) . "</div>";
					echo "<div class='address'>" . $arr[$i]->street .  " ,  " . $arr[$i]->housenumber . " ,  " . $arr[$i]->postalcode . "</div>";
					echo "<div>" . (is_numeric($arr[$i]->countHelper) ? "Anzahl der Helfer: " . $arr[$i]->countHelper : '') ."</div><div class='trustLevel'>Das Mindest Vertraunslevel betraegt " . $arr[$i]->idTrust . "</div>";
					echo "<div>" . $arr[$i]->organization . "</div>";
				echo "</div></a>";
				echo "<br><br><hr><br><br>";
			}
			
		?>
<br><hr>	

</form>

<!--Zurück / Weiter Buttons-->
<?php
	$placeholder = 'alle';
	$vor = $_GET['page'] > 1;
	$nach = db_getGuteTatenForList(10*($_GET['page']), 10,$placeholder); //Sobald Deeds nicht mehr doppelt ausgegeben werden -> Mit Anzahl prüfen!
	if ($vor) echo '<a href="./deeds?page=' . ($_GET['page']-1) . '"><input type="button" value="Zurück"></a>';
	if ($vor && $nach) echo '&nbsp';
	if ($nach) echo '<a href="./deeds?page=' . ($_GET['page']+1) . '"><input type="button" value="Weiter"></a>';
?>

<br> 


<?php
require './includes/_bottom.php';
?>
