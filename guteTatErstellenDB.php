<?php
/*
	//Temporär (wird später aus der Datenbank geladen)
	function db_fix_plz($plz) {
		$db = db_connect();
		$sql = "SELECT postalcode from Postalcode where postalcode = ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('i',$plz);
		$stmt->execute();
		$result = $stmt->get_result();
		if (!isset($result->fetch_assoc['postalcode'])) {
			$sql = 'INSERT INTO Postalcode (postalcode, place) VALUES (?, "Unbekannt")';
			$stmt = $db->prepare($sql);
			$stmt->bind_param('i',$plz);
			$stmt->execute();
		}
		db_close($db);
	}
*/

/*
*@author Christian Hock, Nick Nolting
*Verlinkung zu Orten fehlt
*Kategorie soll editierbar sein
*
*DateHandler eingebunden | Henrik Huckauf
*/

require './includes/DEF.php';
require './includes/UTILS.php';

require './includes/db_connector.php';

require './includes/_top.php';


if (isset($_POST['name'])) {
	$name= ($_POST['name']);
	$description= ($_POST['description']);
	$category= $_POST['kategorie'];
	$street= $_POST['street'];
	$housenumber= $_POST['housenumber'];
	$postalcode= $_POST['postalcode'];
	$place= $_POST['place'];
	$starttime= $_POST['starttime'];
	$endtime= $_POST['endtime'];
	$organization= $_POST['organization'];
	$countHelper= $_POST['countHelper'];
	$idTrust= $_POST['tat_verantwortungslevel'];


	//TIMM: Übergangsweise wegen DB funktion
	$pictures= '';
}
/*0-> gerade erst erstellt 
  1-> bewilligt
  2-> warte auf Antwort
  3-> abgelehnt
*/
$status= 0; 
/*wird auf 1 gesetzt bei falscheingabe*/
$falscheEingabe=0;
/*
$db = db_connect();

	//Testen ob Name schon vorhanden
	$sql = "SELECT name FROM Deeds WHERE name = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('s',$name);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();*/
	if(db_doesGuteTatNameExists($name)){
		echo '<h3>Eine andere Tat ist bereits unter diesem Namen veröffentlicht.</h3>';
		$falscheEingabe=1;
		
	}
	//Ende Test ob Name vorhanden

	//Einfügen der Tat
	if($falscheEingabe==0){

		//Fehlerhafte Eingaben finden
				
				$error = '';

				//Name der guten Tat
				if ($name === '')
					$error .= '<li>Es wurde kein Name für die gute Tat vergeben.</li>';

				//Falls eine fehlerhafte PLZ angegeben wird
				if (!is_numeric($postalcode))
					$error .= '<li>Bitte Postleitzahl überprüfen! Als Postleitzahl sind nur fünfstellige Zahlen erlaubt.</li>';

				//Startzeitpunkt
				if (!DateHandler::isValid($starttime))
					$error .= '<li>Es wurde kein korrektes Startzeitpunkt für die gute Tat festgelegt.</li>';

				//Endzeitpunkt
				if (!DateHandler::isValid($endtime))
					$error .= '<li>Es wurde kein korrektes Endzeitpunkt für die gute Tat festgelegt.</li>';

				if (!db_getIdPostalbyPostalcodePlace($postalcode,$place))
					$error .= '<li>Derzeit sind leider nur Adressen in Hannover möglich. Deine Adresse wird nicht angenommen?<br><a href="./contact">Kontaktiere uns hier</a></li>';

				//Anzahl Helfer keine Zahl
				if (!is_numeric($countHelper))
					$error .= '<li>Bitte Anzahl der Helfer überprüfen! Als Anzahl muss eine einfache Zahl eingegeben werden.</li>';

				if ($error != '')
					die ('<h3>Uhpps..</h3>Beim Erstellen deiner guten Tat ist leider etwas schief gegangen... bitte überprüfe die folgenden Dinge:<ul align="left" style="margin-left:30%">' . $error . '</ul><input type="button" onclick="history.go(-1)" value="Eingaben korrigieren">');
	
		//Einfügen der Guten Tat
		$uid = db_idOfBenutzername($_USER->getUsername());
		//db_fix_plz($postalcode);
		$plz = db_getIdPostalbyPostalcodePlace($postalcode,$place);
		db_createGuteTat($name, $uid, $category, $street, $housenumber, 
			$plz, $starttime,$endtime, $organization, $countHelper, $idTrust,
			$description, $pictures);

		//Versenden der Info-Mails
		
		//Bestimmen der Empfänger
		$mods = db_getAllModerators();
		$admins = db_getAllAdministrators();

		//Festlegen des Mail-Inhalts
		$mailSubject = 'Gute Tat ' . "'" . $_POST['name'] . "'" . ' wurde erstellt!';
		$mailContent1 = '<div style="margin-left:10%;margin-right:10%;background-color:#757575"><img src="img/wLogo.png" alt="TueGutes" title="TueGutes" style="width:25%"/></div><h2>Hallo!';
		$mailContent2 = '</h2><br>' . $_USER->getUsername() . ' hat gerade eine neue gute Tat erstellt. <br>Um die gute Tat zu bestätigen oder abzulehnen, klicke auf den folgenden Link: <br><a href="' . $HOST . '/deeds_details?id=' . db_getIDOfGuteTatByName($_POST['name']) . '">Zur guten Tat</a>';

		//Versenden der Emails an Moderatoren
		for ($i = 0; $i < sizeof($mods); $i++) {
			sendEmail($mods[$i], $mailSubject, $mailContent1 . $mailContent2);
		}
		//Versenden der Emails an Administratoren
		for ($i = 0; $i < sizeof($admins); $i++) {
			sendEmail($admins[$i], $mailSubject, $mailContent1 . $mailContent2);
		}


		/*
		$sql='INSERT INTO Deeds (name, contactPerson, category,street,housenumber,postalcode,time,organization,countHelper,idTrust) VALUES (?,?,?,?,?,?,?,?,?,?)';
		$stmt = $db->prepare($sql);
		$stmt->bind_param('sisssissii', $name, $uid, $category, $street, $housenumber, $postalcode, $starttime, $organization, $countHelper, $idTrust);
		if (!$stmt->execute())
			die ('mysql-error: ' . mysqli_error($db));

		//Laden des maximalen Index
		$sql = 'SELECT MAX(idGuteTat) AS "index" FROM Deeds';
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$result = $stmt->get_result()->fetch_assoc();
		if (isset($result['index'])) {
			$index = $result['index'];
		} else {
			$index = 0;
		}

		//Einfügen der DeedsTexts
		$sql='INSERT INTO DeedTexts (idDeedTexts, description, pictures) VALUES (?,?,?)';
		$stmt = $db->prepare($sql);
		$stmt->bind_param('iss' , $index, $description, $pictures);
		$stmt->execute();
		*/
		echo '<h3>Ihre Tat wurde erfolgreich erstellt und wird nun von uns geprüft und freigegeben.</h3>';
	}
?>	

<br><br>
<div class="center">
	<a href="./guteTatErstellenHTML"><input type="button" value="Noch eine gute Tat erstellen"/></a><br>
	<a href="./deeds"><input type="button" value="zur Übersicht"/></a>
</div>




<?php
require './includes/_bottom.php';
?>