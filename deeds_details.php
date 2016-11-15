<?php
/*
*@author Lukas Buttke, Nick Nolting
*/

require './includes/DEF.php';

include './includes/ACCESS.php';

include "./includes/Map.php";
include './includes/db_connector.php';

require './includes/_top.php';

$myRole = db_get_user($_USER->getUsername())['groupDescription'];
if (!(db_istFreigegeben($_GET['id']) || $myRole=='Moderator' || $myRole=='Administrator'))
	die ('Diese gute Tat muss zuerst von einem Moderator freigegeben werden.<br><a href="./deeds">Schade...</a>');

//------------Einlesen der Daten---------------
$idTat = $_GET["id"];
$tat = db_getGuteTat($idTat);

if (!isset($tat['name']))
	die ('Ungültiger Parameter: Page=' . $_GET['page'] . '<p />Zu dieser ID konnte keine gute Tat gefunden werden.<p />Du meinst das ist ein Fehler? <a href="'.$HOST.'contact">Kontaktiere uns!</a>');

$erstellerName = db_getUsernameOfContactPersonByGuteTatID($idTat);
$erstellerEmail = db_getEmailOfContactPersonByGuteTatID($idTat);

//Gute Tat freigeben oder ablehnen (inkl. Bestätigungsmail an den Ersteller):
$gutetat = db_getGuteTat($_GET['id']);

if (isset($_POST['allow'])) {
	db_guteTatFreigeben($_GET['id']);
	$mailText = '<h3>Hallo ' . $erstellerName . '</h3><p>Deine gute Tat "' . $tat['name'] . '" wurde gerade von ' . $_USER->getUsername() . ' freigegeben.</p>';
	$mailText .= '<a href="' . $HOST . '/deeds_details?id=' . $idTat . '">Klicke hier</a>, um sofort zu deiner guten Tat zu gelangen.';
	sendEmail($erstellerEmail, '"' . $tat['name'] . '" wurde angenommen!', $mailText);
	Header("Refresh: 0");
} else if (isset($_POST['deny'])) {
	//TODO: Beim ablehnen soll nicht einfach dieser Block aufgerufen werden. 
	//Stattdessen soll über "die" ein weiteres Formular mit einem Feld für 
	//die Begründung angezeigt werden. Erst wenn auch das abgeschickt ist, 
	//soll dieser Block ausgeführt und die Begründung in die Email integriert werden.
	db_guteTatAblehnen($_GET['id']);
	$mailText = '<h3>Hallo ' . $erstellerName . '</h3><p>Deine gute Tat "' . $tat['name'] . '" wurde gerade von ' . $_USER->getUsername() . ' abgelehnt.</p>';
	$mailText .= '<a href="' . $HOST . '/guteTatErstellenHTML">Klicke hier</a>, um eine neue gute Tat zu erstellen.';
	sendEmail($erstellerEmail, '"' . $tat['name'] . '" wurde abgelehnt', $mailText);
	die ('Die gute Tat wurde abgelehnt. Der Ersteller der guten Tat wird per Email darüber informiert.<br><a href="'.$HOST.'/deeds">Zurück zur Übersicht</a>');
}

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
$blTaten = '<table width="65%"> <tr> <td width="25%"> Kategorie: </td> <td style="padding:10px">'.$tat["category"].'</td> </tr>';
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

if (!db_istFreigegeben($_GET['id'])) {
	$form1 = '<form method="post" action="">';
	$form1 .= '<input type="submit" value="Gute Tat freigeben" width>';
	$form1 .= '<input type="hidden" name="allow" width>';
	$form1 .= '</form>';

	$form2 = '<form method="post" action="">';
	$form2 .= '<input type="submit" value=" Gute Tat ablehnen ">';
	$form2 .= '<input type="hidden" name="deny" width>';
	$form2 .= '</form>';

	echo $form1 . '<br>' . $form2;
}
else if($_USER->loggedIn() && $_USER->getUsername() == $tat["username"]) {

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
