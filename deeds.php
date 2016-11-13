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
			
			$tatenProSeite=10; //anzahl der tataen die pro seite angezeigt werden
			$arr = db_getGuteTatenForList($tatenProSeite*($_GET['page']-1), $tatenProSeite, $placeholder);

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
	
<?php

$anzahlAllerTaten=77; //db_getGuteTatenAnzahl(); //zu test zwecken wurde hier die zahl 77 eingefügt 
$aktuelleSeite=1;// dieser wert miss mit eins beginnen und die seiten zahl annehmen die der user anklickt zB nächste seite


if($anzahlAllerTaten%$tatenProSeite==0){
	$seitenanzahl=$anzahlAllerTaten/$tatenProSeite;// berechnet die benötigte seiten anzahl
}else{
	$seitenanzahl=$anzahlAllerTaten/$tatenProSeite+1; // berechnet die benötigte seiten anzahl
}
$maxSeitenLinks=7 ; // die menge der gleichzeitig angezeigten seiten zB bei 7 -> 3 4 5 6 7 8 9 wir befinden uns auf seite 6
			
			function zahlenausgabe($von,$bis){//schreibt die seiten zahlen
				for($i=$von;$i<=$bis;$i++){
					//der link get auf eine falsche seite er muss diese seite nochmal aufgerufen werden nur mit der pasenden seiten nummer
					echo /*'<a href="./deeds?page=' . ($1) . '">*/'<div>'. $i .'</div></a>';// die zahlen stehen untereinander !!! fehler die sollen natuerlich nebeneinander stehen
				}
			}
			
			if($seitenanzahl>$maxSeitenLinks){
				if($aktuelleSeite<($maxSeitenLinks/2)+1){
					zahlenausgabe(1,$maxSeitenLinks);
				}else{
					printf ("<a href='./deeds'><div> Erste Seite </div>");// der link geht jetzt
					zahlenausgabe($aktuelleSeite-3,$aktuelleSeite+3);
				}
				printf ("<a href='./deeds_details?id=" . $i . "' class='deedAnchor'><div> Letzte Seite </div>");// die links sind falsch kp wie ich an die atribute kommen die die seiten hoch zaehlen
			}else{
				zahlenausgabe(1,$seitenanzahl);
			}
?>	

</form>

<!--Zurück / Weiter Buttons-->
<?php
	$placeholder = 'alle';
	$vor = $_GET['page'] > 1;
	$nach = db_getGuteTatenForList($tatenProSeite*($_GET['page']), $tatenProSeite, $placeholder); //Sobald Deeds nicht mehr doppelt ausgegeben werden -> Mit Anzahl prüfen!
	if ($vor) echo '<a href="./deeds?page=' . ($_GET['page']-1) . '"><input type="button" value="Zurück"></a>';
	if ($vor && $nach) echo '&nbsp';
	if ($nach) echo '<a href="./deeds?page=' . ($_GET['page']+1) . '"><input type="button" value="Weiter"></a>';
?>

<br> 


<?php
require './includes/_bottom.php';
?>
