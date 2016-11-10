<?php
/*
*@author Lukas Buttke, Nick Nolting
*/

require './includes/DEF.php';

include './includes/ACCESS.php';

include "./includes/Map.php";
include './includes/db_connector.php';

require './includes/_top.php';

//------------Einlesen der Daten---------------
$idTat = $_GET["id"];
$tat = db_getGuteTat($idTat);
?>

<style>
	#mapid {
		height:350px;
		width:350px;
	}
</style>

<?php

// --------Erstellen von Blöcken zur Formatierten ausgabe
$blAbout = '<h2>'.$tat["name"] .'</h2>';
//$blAbout .= ' Gute Tat #'.$idTat.' </h>';

// -----------Gute Taten Details - genauer
$blTaten = '<table width="60%"> <tr> <td> Kategorie: </td> <td style="padding:10px">'.$tat["category"].'</td> </tr>';
$blTaten .= '<tr> <td> Kontaktperson: </td> <td style="padding:10px"> <a href="./profile?user='.$tat["username"].'">'.'<img src="' . $tat["avatar"] . '" style="height:3%;float:left" >&nbsp'.$tat["username"].'</a> </td>';
$blTaten .= '<tr> <td> Gewünschter Vertrauenslevel: </td> <td style="padding:10px">'.$tat["idTrust"]. ' ('.$tat['trustleveldescription'].')' .'</td> </tr>';
$blTaten .= '<tr> <td> Beschreibung: </td> <td style="padding:10px">'.(($tat['description']!='')?$tat["description"]:'keine Beschreibung angegeben').'</td> </tr>';
if ($tat['starttime']!='0000-00-00 00:00:00') $blTaten .= '<tr> <td> Beginn: </td> <td style="padding:10px">'.$tat['starttime'].'</td> </tr>';
if ($tat['endtime']!='0000-00-00 00:00:00') $blTaten .= '<tr> <td> Ende: </td> <td style="padding:10px">'.$tat['endtime'].'</td> </tr>';
if ($tat['organization']!='') $blTaten .= '<tr> <td> Organisation: </td> <td style="padding:10px">'.$tat["organization"].'</td> </tr>';
$blTaten .= '<tr> <td> Anzahl Helfer: </td> <td style="padding:10px">'.$tat["countHelper"].'</td> </tr> </table>';

// -------------- Einbindung der Map -------------------------
$blMap = '<h3>Adresse der Guten Tat:</h3>';

$shPlzOrt = isset($tat["postalcode"]) && $tat['postalcode'] != '';
$shStrasse = isset($tat["street"]) && $tat['street'] != '';
$shHausnummer = isset($tat["housenumber"]) && $tat['housenumber'] != '';
$showMap = ($shPlzOrt && $shStrasse && $shHausnummer);


// --------------- Ausgabe der Blöcke, eingepackt in div boxen ----------
echo '<div align="center">' . $blAbout . '</div>';
echo '<p />';
echo '<div align="center">' . $blTaten . '</div>';
echo '<p />';
echo '<div align="center">' .$blMap;

if ($showMap) {
		echo '<div id="mapid">';
		createMap($tat['postalcode'] . ',' . $tat['street'] . ',' . $tat['housenumber']);
		echo '</div>';
	}
else{
	echo '<div align="center" style="font-size:200%;">'.'Adresse wurde gar nicht oder <br> nur unvollständig angegeben! ';
}

echo '</div>';
echo '<p />';

echo '<br> <hr> <br> ';
if($_USER->loggedIn() && $_USER->getUsername() == $tat["username"]) {

$link = './deeds_bearbeiten?id='.$idTat;

$form = '<form method="post" action="'.$link.'">';
$form .= '<input type="submit" value="Bearbeiten">';
$form .= '</form>';

echo $form;
}
else{

$link = 'deeds_bewerbung?idGuteTat='.$idTat; 

$form = '<form method="post" action="'.$link.'">';
$form .= '<input type="submit" value="Bewerben">';
$form .= '</form>';

echo $form;
}

?>

<?php
require './includes/_bottom.php';
?>
