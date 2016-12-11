<?php
/*
*@author Lukas Buttke, Nick Nolting
*/

require './includes/DEF.php';

include './includes/ACCESS.php';

include "./includes/Map.php";
include './includes/db_connector.php';

require './includes/_top.php';

if (!isset($_GET['id']))
	$_USER->redirect("./deeds");

$idTat = $_GET["id"];
$tat = DBFunctions::db_getGuteTat($idTat);

if (isset($_POST['delete'])) {
	if ($_POST['delete']=='false') {
		die ('<h3>Gute Tat löschen</h3>Damit wird die gute Tat unwiderruflich gelöscht! Bist du sicher?<form method="POST" action=""><input type="submit" value="Entgültig löschen"><input type="hidden" name="delete" value="true"></form>');
	} else {
		DBFunctions::db_deleteDeed($idTat);
		$_USER->redirect("./deeds");
	}
}

if (!isset($tat['name']))
	die ('Ungültiger Parameter: Page=' . $_GET['page'] . '<p />Zu dieser ID konnte keine gute Tat gefunden werden.<p />Du meinst das ist ein Fehler? <a href="'.$HOST.'contact">Kontaktiere uns!</a>');

$erstellerName = DBFunctions::db_getUsernameOfContactPersonByGuteTatID($idTat);
$erstellerEmail = DBFunctions::db_getEmailOfContactPersonByGuteTatID($idTat);

//Gute Tat freigeben oder ablehnen (inkl. Bestätigungsmail an den Ersteller):
$gutetat = DBFunctions::db_getGuteTat($_GET['id']);

//Falls zwei Moderatoren die gleiche Tat gleichzeitig bearbeiten:
if (DBFunctions::db_istFreigegeben($_GET['id']) && (isset($_POST['allow']) || isset($_POST['dontAllow']) || isset($_POST['deny'])))
	die ('Ein anderer Moderator hat diese Tat bereits bearbeitet. Vielen Dank für deine Mühe!');

if (isset($_POST['allow'])) {
	DBFunctions::db_guteTatFreigeben($_GET['id']);
	$mailText = '<h3>Hallo ' . $erstellerName . '</h3><p>Deine gute Tat "' . $tat['name'] . '" wurde gerade von ' . $_USER->getUsername() . ' freigegeben.</p>';
	$mailText .= '<a href="' . $HOST . '/deeds_details?id=' . $idTat . '">Klicke hier</a>, um sofort zu deiner guten Tat zu gelangen.';
	sendEmail($erstellerEmail, '"' . $tat['name'] . '" wurde angenommen!', $mailText);
	Header("Refresh: 0");
} else if (isset($_POST['dontAllow'])) {
	$form3 = '<form method="post" action="">';
	$form3 .= '<textarea rows=20 cols=100 placeholder="Bitte begründe deine Entscheidung..." name="txtBegründung">' . '</textarea><br><br><br>';
	$form3 .= '<input type="submit" value=" Gute Tat ablehnen ">';
	$form3 .= '<input type="hidden" name="deny" width>';
	$form3 .= '</form>';
	die($form3);
} else if (isset($_POST['deny'])) {
	DBFunctions::db_guteTatAblehnen($_GET['id']);
	$mailText = '<h3>Hallo ' . $erstellerName . '</h3><p>Deine gute Tat "' . $tat['name'] . '" wurde gerade von ' . $_USER->getUsername() . ' abgelehnt. Als Begründung für diese Entscheidung schreibt ' . $_USER->getUsername() . ':</p><p>' . $_POST['txtBegründung'] . '</p>';
	$mailText .= '<a href="' . $HOST . '/guteTatErstellenHTML">Klicke hier</a>, um eine neue gute Tat zu erstellen.';
	sendEmail($erstellerEmail, '"' . $tat['name'] . '" wurde abgelehnt', $mailText);
	die ('Die gute Tat wurde abgelehnt. Der Ersteller der guten Tat wird per Email darüber informiert.<br><a href="'.$HOST.'/deeds">Zurück zur Übersicht</a>');
}

	$myRole = DBFunctions::db_get_user($_USER->getUsername())['groupDescription'];
	if (!(DBFunctions::db_istFreigegeben($idTat) || $myRole=='Moderator' || $myRole=='Administrator')){
		die ('Diese gute Tat muss zuerst von einem Moderator freigegeben werden.<br><a href="./deeds">Schade...</a>');
	}

	if ((DBFunctions::db_istGeschlossen($idTat))){
		die ('Diese Tat wurde bereits vom Besitzer geschlossen !<br><a href="./deeds">gehe zurück...</a>');
	}

	//------------Einlesen der Daten---------------


	if (!isset($tat['name']))
		die ('Ungültiger Parameter: Page=' . $_GET['page'] . '<p />Zu dieser ID konnte keine gute Tat gefunden werden.<p />Du meinst das ist ein Fehler? <a href="'.$HOST.'contact">Kontaktiere uns!</a>');

	$erstellerName = DBFunctions::db_getUsernameOfContactPersonByGuteTatID($idTat);
	$erstellerEmail = DBFunctions::db_getEmailOfContactPersonByGuteTatID($idTat);

	//Gute Tat freigeben oder ablehnen (inkl. Bestätigungsmail an den Ersteller):
	$gutetat = DBFunctions::db_getGuteTat($_GET['id']);

	if (isset($_POST['allow'])) {
		DBFunctions::db_guteTatFreigeben($_GET['id']);
		$mailText = '<h3>Hallo ' . $erstellerName . '</h3><p>Deine gute Tat "' . $tat['name'] . '" wurde gerade von ' . $_USER->getUsername() . ' freigegeben.</p>';
		$mailText .= '<a href="' . $HOST . '/deeds_details?id=' . $idTat . '">Klicke hier</a>, um sofort zu deiner guten Tat zu gelangen.';
		sendEmail($erstellerEmail, '"' . $tat['name'] . '" wurde angenommen!', $mailText);
		Header("Refresh: 0");
	} else if (isset($_POST['deny'])) {
		//TODO: Beim ablehnen soll nicht einfach dieser Block aufgerufen werden. 
		//Stattdessen soll über "die" ein weiteres Formular mit einem Feld für 
		//die Begründung angezeigt werden. Erst wenn auch das abgeschickt ist, 
		//soll dieser Block ausgeführt und die Begründung in die Email integriert werden.
		DBFunctions::db_guteTatAblehnen($_GET['id']);
		$mailText = '<h3>Hallo ' . $erstellerName . '</h3><p>Deine gute Tat "' . $tat['name'] . '" wurde gerade von ' . $_USER->getUsername() . ' abgelehnt.</p>';
		$mailText .= '<a href="' . $HOST . '/guteTatErstellenHTML">Klicke hier</a>, um eine neue gute Tat zu erstellen.';
		sendEmail($erstellerEmail, '"' . $tat['name'] . '" wurde abgelehnt', $mailText);
		die ('Die gute Tat wurde abgelehnt. Der Ersteller der guten Tat wird per Email darüber informiert.<br><a href="'.$HOST.'/deeds">Zurück zur Übersicht</a>');
	}

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
	$blTaten .= '<tr> <td > Anzahl Helfer: </td> <td id="helfer" style="padding:10px">'.$tat["countHelper"].'</td> </tr> </table>';

	// -------------- Einbindung der Map -------------------------
	$blMap = '<h3>Adresse der Guten Tat:</h3>';

	$shPlzOrt = isset($tat["postalcode"]) && $tat['postalcode'] != '';
	//$shStrasse = isset($tat["street"]) && $tat['street'] != '';
	//$shHausnummer = isset($tat["housenumber"]) && $tat['housenumber'] != '';
	$showMap = ($shPlzOrt);


	// --------------- Ausgabe der Blöcke, eingepackt in div boxen ----------
	echo '<div class="center">' . $blAbout . '</div>';
	echo '<p />';
	echo '<div class="center">' . $blTaten . '</div>';
	echo '<p />';
	echo '<div class="center">' .$blMap;

	if ($showMap) {
			$map = new Map();
			$map->createSpace('0%','350px','350px');
			$map->createMap($tat['postalcode'] . ',' . $tat['street'] . ',' . $tat['housenumber']);
		}
	else{
		echo '<div class="center" style="font-size:200%;">'.'Adresse wurde gar nicht oder <br> nur unvollständig angegeben! ';
	}

	echo '</div>';
	echo '<p />';

	echo '<br> <hr> <br> ';

	if (!DBFunctions::db_istFreigegeben($idTat)) {
		$form1 = '<form method="post" action="">';
		$form1 .= '<input type="submit" value="Gute Tat freigeben" width>';
		$form1 .= '<input type="hidden" name="allow" width>';
		$form1 .= '</form>';

		$form2 = '<form method="post" action="">';
		$form2 .= '<input type="submit" value=" Gute Tat ablehnen ">';
		$form2 .= '<input type="hidden" name="dontAllow" width>';
		$form2 .= '</form>';

		echo $form1 . '<br>' . $form2;
	} else if(($_USER->loggedIn() && $_USER->getUsername() == $tat["username"])) {
		$link = './deeds_bearbeiten?id='.$idTat;
		$link2 = './deeds_bewerten?id='.$idTat;

		$form = '<form method="post" action="'.$link.'">';
		$form .= '<input type="submit" value="Bearbeiten">';
		$form .= '</form>';

		$form .= '<br>';

		$form2 = '<form method="post" action="'.$link2.'">';
		$form2 .= '<input type="hidden" name="close" value="true">';
		$form2 .= '<input type="submit" value="Schließen">';
		$form2 .= '</form>';

		$form2 .= '<br>';

		$form3 = '<form method="post" action="">';
		$form3 .= '<input type="hidden" name="delete" value="false">';
		$form3 .= '<input type="submit" value="   Löschen   ">';
		$form3 .= '</form>';

		echo $form . $form2 . $form3 . '<br>';
	} else {

		$link = 'deeds_bewerbung?idGuteTat='.$idTat; 

		$form = '<form method="post" action="'.$link.'">';
		$form .= '<input type="submit" value="Bewerben">';
		$form .= '</form>';

		echo $form . '<br>';
	}

	
	echo "<br><br>";
	include './includes/comment.php';

	
?>


<?php
require './includes/_bottom.php';
?>

