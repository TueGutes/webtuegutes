<?php

//Temporär (wird später aus der Datenbank geladen)
	function db_fix_plz($plz) {
		$db = db_connect();
		$sql = "SELECT * from Postalcode where postalcode = ?";
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


/*
*@author Christian Hock
*Verlinkung zu Orten fehlt
*Kategorie soll editierbar sein
*/
include './includes/session.php';
require './includes/_top.php';
require './includes/db_connector.php';

if (isset($_POST['name'])) {
	$name= ($_POST['name']);
	$description= ($_POST['description']);
	$category= $_POST['kategorie'];
	$street= $_POST['street'];
	$housenumber= $_POST['housenumber'];
	$postalcode= $_POST['postalcode'];
	$time_t= $_POST['time'];
	$organization= $_POST['organization'];
	$countHelper= $_POST['countHelper'];
	$idTrust= $_POST['tat_verantwortungslevel'];
}
/*0-> gerade erst erstellt 
  1-> bewilligt
  2-> warte auf Antwort
  3-> abgelehnt
*/
$status= 0; 
/*wird auf 1 gesetzt bei falscheingabe*/
$falscheEingabe=0;

$db = db_connect();

	//Testen ob Name schon vorhanden
	$sql = "SELECT name FROM Deeds WHERE name = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('s',$name);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	if(isset($dbentry['name'])){
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
					$error .= 'Es wurde kein Name für die gute Tat vergeben.<br>';

				//Falls eine fehlerhafte PLZ angegeben wird
				if (!is_numeric($postalcode))
					$error .= 'Bitte Postleitzahl überprüfen! Als Postleitzahl sind nur fünfstellige Zahlen erlaubt.<br>';

				//Zeitrahmen
				if ($time_t === '')
					$error .= 'Es wurde kein Zeitrahmen für die gute Tat festgelegt.<br>';

				//Anzahl Helfer keine Zahl
				if (!is_numeric($countHelper))
					$error .= 'Bitte Anzahl der Helfer überprüfen! Als Anzahl muss eine einfache Zahl eingegeben werden.<br>';

				if ($error != '')
					die ('Da ist etwas schief gegangen... bitte überprüfe die folgenden Fehler:<br>' . $error . '<input type="button" onclick="history.go(-1)" value="Eingaben korrigieren">');
	
		//Einfügen der Guten Tat
		$uid = db_idOfBenutzername($_SESSION['user']);
		$sql='INSERT INTO Deeds (name, contactPerson, category,street,housenumber,postalcode,time,organization,countHelper,idTrust) VALUES (?,?,?,?,?,?,?,?,?,?)';
		$stmt = $db->prepare($sql);
		$stmt->bind_param('sisssissii', $name, $uid, $category, $street, $housenumber, $postalcode, $time_t, $organization, $countHelper, $idTrust);
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
		echo '<h3>Ihre Tat wurde erfolgreich erstellt und wird nun von uns geprüft und freigegeben.</h3>';
		include './buttonsTatErstellen.html';
	} else {
		include './buttonsTatErstellen.html';
	}
	//Ende einfügen der Tat	
	db_close($db);	





require './includes/_bottom.php';
?>