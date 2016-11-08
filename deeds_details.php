<?php
/*
*@author Lukas Buttke
*/

include "./includes/Map.php";
include './includes/ACCESS.php';
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
$blAbout = '<h2>'.$tat["name"] .'<br>';
$blAbout .= ' Gute Tat #'.$idTat.' </h>';

// ----------User, welcher das erstellt hat.
// Ich würde gerne einen Link von hier auf das jeweilige Benutzerprofil machen
$blSelf = '<br> <img src="' . $tat["avatar"] . '" width="25" height="25" >';
$blSelf .= '<a href="profile.php?user='.$tat["username"].'">'.$tat["username"].'</a>';

$blComb = '<table> <tr> <td> '.$blSelf.'</td> <td>
&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
</td> <td style="width:280px" >'.$blAbout.' </td> </tr> </table> <hr>';

// -----------Gute Taten Details - genauer
$blTaten = '<table> <tr> <td> Categorie </td> <td>'.$tat["category"].'</td> </tr>';
$blTaten .= '<tr> <td> <br>Beschreibung: <br> '.$tat["description"].'</td> </tr>';
$blTaten .= '<tr> <td> <br>Zeitpunkt: </td> <td> <br>'.$tat["time"].'</td> </tr>';
$blTaten .= '<tr> <td> <br>Organisation: </td> <td> <br>'.$tat["organization"].'</td> </tr>';
$blTaten .= '<tr> <td> <br>Anzahl Helfer: </td> <td> <br>'.$tat["countHelper"].'</td> </tr> </table>';

// -------------- Einbindung der Map -------------------------
$blMap = 'Adresse der Guten Tat '.'<hr>';

$shPlzOrt = isset($tat["postalcode"]);
$shStrasse = isset($tat["street"]);
$shHausnummer = isset($tat["housenumber"]);
$showMap = ($shPlzOrt && $shStrasse && $shHausnummer);

// --------- Vertrauenslevel
$blTrust = '<table> <tr> <td> benötigtes Vertrauenslevel <br> ----------> </td> <td> ';
$blTrust .= '&nbsp &nbsp &nbsp'.$tat["idTrust"]. '<br> <b> ';
$blTrust .= $tat["trustleveldescription"].'</b> </td> </tr> </table>';


// --------------- Ausgabe der Blöcke, eingepackt in div boxen ----------
echo '<div align="center">' . $blComb . '</div>';
echo '<p />';
echo '<div align="center" style="font-size:130%;">' . $blTaten . '</div>';
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
echo '<div align="center" style="font-size:140%;"> <hr>' . $blTrust . '</div>';
echo '<p />';

echo '<br> <hr> <br> ';
if((isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']) && (isset($_SESSION['user']) && ($_SESSION['user'] == $tat["username"]))) {

$link = 'deeds_bearbeiten.php?idGuteTat='.$idTat; 

$form = '<form method="post" action="'.$link.'">';
$form .= '<input type="submit" value="Bearbeiten">';
$form .= '</form>';

echo $form;
}
else{
	
$link = 'deeds_bewerben.php?idGuteTat='.$idTat; 

$form = '<form method="post" action="'.$link.'">';
$form .= '<input type="submit" value="Bewerben">';
$form .= '</form>';

echo $form;
}

?>

<a href='./deeds_details?id=" . $arr[$i]->idGuteTat .
<?php
require './includes/_bottom.php';
?>
