<?php
/*
*@author Lukas Buttke
*/

//Nötige Header/Dateien
require './includes/DEF.php';
include './includes/ACCESS.php';
require './includes/_top.php';

//Laden der aktuellen Gute Tat-ID und schließen der Guten Tat
//Ausgabe falls die Gute Tat geschlossen wurde und dazugehörige Rückmeldung
$idTat  = $_GET['id'];
DBFunctions::db_guteTatClose($idTat); 
if ((DBFunctions::db_istGeschlossen($idTat))){
	echo '<h3> Ihre Tat wurde nun geschlossen ! Bitte bewerten Sie ihre Helfer. </h>';
}
//Laden der Bewerbungen
$db = DBFunctions::db_getBewerb($idTat);
//Anzahl der Bewerbungen summieren
$durchlauf = count($db);
$arrUser = array();
//Aufbauen des Dropdowns Menü für das Bewerten jedes Bewerbers/Teilnehmers der Guten Tat
$form = '<form action="" method="post">';
for ($i = 0; $i < $durchlauf; $i++) {
	foreach($db as $key){
		$username = DBFunctions::db_getUsernameOfBenutzerByID($key);
		$arrUser[$i] = $username;
		$form .= '<center> <table> <tr> <td> <b>'.$username.'<b> </td>';
		$form .= '<td>  <select name="'.$username.'" size="1">';
		$form .= '<option value="1">1</option> <option value="2">2</option> <option value="3">3</option>';
		$form .= '<option value="4">4</option> <option value="5">5</option> <option value="0">Keine Bewertung</option>';
		$form .= '</select> </td> <tr> </table> </center>';
	}
}
$form .='<input type="hidden" value="set" name="test">';
$form .='<input type="submit" value="absenden"> </form> <br> <hr>';
if(!isset($_POST['test'])){
	echo $form;
}
//Übernemehn der Bewertungen in die Datenbank
if(isset($_POST['test'])){
	$tmp = count($arrUser);
	$out = "";
	for($i = 0; $i < $tmp; $i++){
		$name = $arrUser[$i];
		$extraPoints = $_POST[$name];
		$thisuser = DBFunctions::db_get_user($name);
		if(!is_null($thisuser)){
			$userPoint = $thisuser['points'];
			$userTrust = $thisuser['idTrust'];
			$points = $userPoint + $extraPoints;
			DBFunctions::db_userBewertung($points,$name);
			if($points <= 120){
				$round = floor(($points/20));
				$trust = $round + 1;
				DBFunctions::db_userAnsehen($trust,$name);
			}
		}
		$out .= '<h5> Sie haben '.$username.' mit '.$extraPoints.' Punkten bewertet! </h>';	
	}
	$out .= '<h5> Mit steigender Punktzahl steigt auch das Vertrauen </h>';
	$out .= '<hr> <a href="./deeds.php"> <input type="Button" value="Zurück"> </a> ';
	echo $out;
}
require './includes/_bottom.php';
?>