<?php
/**
 * Adminseite für Taten

 * @author Christian Hock
   @ Angelehnt an Deeds.php
 */

//require './includes/DEF.php';

include './includes/ACCESS.php';

require './includes/_top.php';

include './includes/Map.php';

if(!$_USER->hasGroup($_GROUP_MODERATOR))
{
	$_USER->redirect('./error?e=404');
	exit;
}

if(!isset($_GET['p'])) $_GET['p'] = 1  && $placeholder="Alle";
if (!isset($_POST['status'])) {
		$_POST['status'] = 'alle';
		if(!isset($_REQUEST['adt'])){
			$_REQUEST['adt'] = 10;
		}
		
		$first=TRUE;
	} else {
		$first=FALSE;
	}
?>

<h2><?php echo $wlang['deeds_head']; ?> </h2>
	<div class="fb-like" 
		data-href="https://www.facebook.com/tueGutesinHannover" 
		data-width="350" 
		data-layout="standard" 
		data-action="like" 
		data-size="small" 
		data-show-faces="true" 
		data-share="false">
	</div>
<div>
<form action="deeds_create" method="post">
	<input type="hidden" name="opened">
	<input type="submit" value="Gute Tat erstellen" target="_self">
	<br><hr>
	</div>
	<br> 
	</form>
	
		<form method="post" action="">
			<h5>
				Anzeigen:
				<select name="status" size="1" onchange="this.form.submit()">
					<option value="alle" <?php ($first || @$_POST['status']=="alle")?'selected':'' ?> >alle</option>                                   <?/*hier wird der status abgefragt */?>
					<option value="freigegeben" <?php echo (@$_POST['status']=="freigegeben")?'selected':'' ?> >Nicht abgeschlossen</option>     <?/*hier wird der status abgefragt */?>
					<option value="geschlossen" <?php echo (@$_POST['status']=="geschlossen")?'selected':'' ?> >abgeschlossen</option>				 <?/*hier wird der status abgefragt */?>
					<?php if($_USER->hasGroup($_GROUP_MODERATOR)){
						?>
						<option value="nichtFreigegeben" <?php echo (@$_POST['status']=="nichtFreigegeben")?'selected':'' ?> >noch nicht freigegeben</option>		
					<?php } ?>
				</select>
				<noscript><input type="submit" name="button" value="Aktualisieren"/></noscript>
				&nbsp;
				Taten pro Seite:
				<select name="adt" size="1" onchange="this.form.submit()">
					<option value="5" <?php echo (@$_REQUEST['adt']==5)?'selected':'' ?> >5</option>        <?/*hier wird der status abgefragt */?>
					<option value="10" <?php echo (@$_REQUEST['adt']==10)?'selected':'' ?> >10</option>     <?/*hier wird der status abgefragt */?>
					<option value="15" <?php echo (@$_REQUEST['adt']==15)?'selected':'' ?> >15</option>	 <?/*hier wird der status abgefragt */?>
					<option value="20" <?php echo (@$_REQUEST['adt']==20)?'selected':'' ?> >20</option>      <?/*hier wird der status abgefragt */?>
					<option value="50" <?php echo (@$_REQUEST['adt']==50)?'selected':'' ?> >50</option>     <?/*hier wird der status abgefragt */?>
					<option value="100" <?php echo (@$_REQUEST['adt']==100)?'selected':'' ?> >100</option>	 <?/*hier wird der status abgefragt */?>
				</select>
				<noscript><input type="submit" name="button" value="Aktualisieren"/></noscript>
			</h5>
		</form>
			<?php
				
				$placeholder = $_POST['status'];
				$tatenProSeite=$_REQUEST['adt'];
				$all = !(isset($_GET['user']) && DBFunctions::db_getIdUserByUsername($_GET['user']!=-1));
				
				if ($all) {
					if($placeholder=='alle'){
						$arr2 = DBFunctions::db_getGuteTatenForList($tatenProSeite*($_GET['p']-1), $tatenProSeite, 'nichtFreigegeben');
						$arr = DBFunctions::db_getGuteTatenForList($tatenProSeite*($_GET['p']-1), $tatenProSeite, $placeholder);
						$arr =array_merge($arr2, $arr);
					}
					else{
						$arr = DBFunctions::db_getGuteTatenForList($tatenProSeite*($_GET['p']-1), $tatenProSeite, $placeholder);
					}
					/*$arr2 = DBFunctions::db_getGuteTatenForList($tatenProSeite*($_GET['p']-1), $tatenProSeite, 'nichtFreigegeben');
					echo 'Test2 nichtfreigegebene:  '.sizeof($arr2).'<br>';
					$arr = DBFunctions::db_getGuteTatenForList($tatenProSeite*($_GET['p']-1), $tatenProSeite, $placeholder);
					echo 'Test2 alle anderen :  '.sizeof($arr).'<br>';
					//$arr =array_merge($arr2, $arr);
					echo 'Ich echo das hier!';
					echo 'Test2:  '.sizeof($arr).'<br>';*/
				}
				else{
					$arr = DBFunctions::db_getGuteTatenForUser($tatenProSeite*($_GET['p']-1), $tatenProSeite, $placeholder, DBFunctions::db_getIdUserByUsername($_GET['user']));
				}

				$maxZeichenFürDieKurzbeschreibung = 150;
				for($i = 0; $i < sizeof($arr); $i++){
					echo "<a href='./deeds_details?id=" . $arr[$i]->idGuteTat . "&admin=true' class='deedAnchor'><div class='deed" . ($arr[$i]->status == "geschlossen" ? " closed" : "") . "'>";
						echo "<div class='name'><h4>" . $arr[$i]->name . "</h4></div><div class='category'>" . $arr[$i]->category . "</div>";
						echo "<br><br><br><br><div class='description'>" . (strlen($arr[$i]->description) > $maxZeichenFürDieKurzbeschreibung ? substr($arr[$i]->description, 0, $maxZeichenFürDieKurzbeschreibung) . "...<br>mehr" : $arr[$i]->description) . "</div>";
						echo "<div class='address'>" . $arr[$i]->street .  "  " . $arr[$i]->housenumber . "<br>" . $arr[$i]->postalcode . ' / ' . $arr[$i]->place . "</div>";
						echo "<div>" . (is_numeric($arr[$i]->countHelper) ? "Anzahl der Helfer: " . $arr[$i]->countHelper : '') ."</div><div class='trustLevel'>Minimaler Vertrauenslevel: " . $arr[$i]->idTrust . " (" . $arr[$i]->trustleveldescription . ")</div>";
						echo "<div>" . $arr[$i]->organization . "</div>";
					echo "</div></a>";
					echo "<br><br><hr><br>";
				}

			if ($all){
				if($placeholder='alle'){
					$anzahlAllerTaten=DBFunctions::db_getGuteTatenAnzahl($placeholder)+DBFunctions::db_getGuteTatenAnzahl('nichtFreigegeben');
					echo 'Test:  '.$anzahlAllerTaten;
				}
				else{
					$anzahlAllerTaten=DBFunctions::db_getGuteTatenAnzahl($placeholder);
					echo 'Test:  '.$anzahlAllerTaten;
				}
			}
				
			else{
				$anzahlAllerTaten=DBFunctions::db_countGuteTatenForUser($placeholder, DBFunctions::db_getIdUserByUsername($_GET['user']));
			}

			$aktuelleSeite=$_GET['p'];
			$letzteseite=intval($anzahlAllerTaten / $tatenProSeite);
			if ($letzteseite * $tatenProSeite < $anzahlAllerTaten) $letzteseite++;

			if($anzahlAllerTaten%$tatenProSeite==0){
				$seitenanzahl=$anzahlAllerTaten/$tatenProSeite;// berechnet die benötigte seiten anzahl
			}else{
				$seitenanzahl=$anzahlAllerTaten/$tatenProSeite+1; // berechnet die benötigte seiten anzahl
			}
			$maxSeitenLinks=7 ; // die menge der gleichzeitig angezeigten seiten zB bei 7 -> 3 4 5 6 7 8 9 wir befinden uns auf seite 6
						
						function zahlenausgabe($von,$bis, $letzteseite, $all){//schreibt die seiten zahlen
							for($i=$von;$i<=$bis;$i++){
								//der link get auf eine falsche seite er muss diese seite nochmal aufgerufen werden nur mit der pasenden seiten nummer
								if ($i>0 && $i<=$letzteseite) echo '<a href="./admin?page=deeds&p=' . $i . (!$all?'&user='.$_GET['user']:'') .'&adt='.($_REQUEST['adt']). '">&nbsp'. $i .'&nbsp</a>';
							}
						}
						
						if($seitenanzahl>$maxSeitenLinks){
							if($aktuelleSeite<($maxSeitenLinks/2)+1){
								zahlenausgabe(1,$aktuelleSeite-1, $letzteseite, $all);
								echo '&nbsp' . $aktuelleSeite . '&nbsp';
								zahlenausgabe($aktuelleSeite+1,$maxSeitenLinks, $letzteseite, $all);
								if ($letzteseite > $maxSeitenLinks) printf ('<a href="./admin?page=deeds&p=' . $letzteseite . (!$all?'&user='.$_GET['user']:'') .'&adt='.($_REQUEST['adt']). '"> ... ' . $letzteseite . '</a>');
							} else if ($aktuelleSeite > $letzteseite- ($maxSeitenLinks/2)-1) {
								if ($letzteseite > $maxSeitenLinks) printf ("<a href='./admin?page=deeds" . (!$all?'?user='.$_GET['user']:'') .'&adt='.($_REQUEST['adt']). "'>". '1 ... ' ."</a>");// der link geht jetzt
								zahlenausgabe($letzteseite-$maxSeitenLinks,$aktuelleSeite-1, $letzteseite, $all);
								echo '&nbsp' . $aktuelleSeite . '&nbsp';
								zahlenausgabe($aktuelleSeite+1,$letzteseite, $letzteseite, $all);
							}else{
								printf ("<a href='./admin?page=deeds" . (!$all?'?user='.$_GET['user']:'') .'&adt='.($_REQUEST['adt']). "'>". '1 ... ' ."</a>");// der link geht jetzt
								zahlenausgabe($aktuelleSeite-intval($maxSeitenLinks/2),$aktuelleSeite-1,$letzteseite, $all);
								echo '&nbsp' . $aktuelleSeite . '&nbsp';
								zahlenausgabe($aktuelleSeite+1,$aktuelleSeite+intval($maxSeitenLinks/2),$letzteseite, $all);
								printf ('<a href="./admin?page=deeds&p=' . $letzteseite . (!$all?'&user='.$_GET['user']:'') .'&adt='.($_REQUEST['adt']). '"> ... ' . $letzteseite . '</a>');
							}
						}else{
							zahlenausgabe(1,$seitenanzahl, $letzteseite, $all);
						}
			?>

			<!--Einbinden der Map-->
			<br><br>
			<?php
			$map = new Map();
			$map->createSpace('10%','500px','80%');
			$map->createDeedsMap($tatenProSeite, $placeholder, (isset($_GET['user'])?DBFunctions::db_getIdUserByUsername($_GET['user']):-1));
			?>
	<br><br><hr>
</form>

<!--Zurück / Weiter Buttons-->
<?php
	//$placeholder = 'alle';
	$vor = $_GET['p'] > 1;
	$nach = DBFunctions::db_getGuteTatenForList($tatenProSeite*($_GET['p']), $tatenProSeite, $placeholder); //Sobald Deeds nicht mehr doppelt ausgegeben werden -> Mit Anzahl prüfen!
	if ($vor) echo '<a href="./admin?page=deeds&p=' . ($_GET['p']-1) .'&adt='.($_REQUEST['adt']). '"><input type="button" value="Zurück"></a>';
	if ($vor && $nach) echo '&nbsp';
	if ($nach) echo '<a href="./admin?page=deeds&p=' . ($_GET['p']+1) .'&adt='.($_REQUEST['adt']). '"><input type="button" value="Weiter"></a>';

	?>

<br> 
<?php
require './includes/_bottom.php';
?>