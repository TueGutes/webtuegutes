<?php
/*
*@author Christian Hock
*Verlinkung zu Orten fehlt
*Kategorie soll editierbar sein
*/
require './includes/_top.php';
require './db_connector.php';
$name= ($_GET['name']);
$description= ($_GET['description']);
$pictures= ($_GET['pictures']);
$contactPerson= $_GET['contactPerson'];
$category= $_GET['kategorie'];
$street= $_GET['street'];
$housenumber= $_GET['housenumber'];
$postalcode= $_GET['postalcode'];
$time_t= $_GET['time'];
$organization= $_GET['organization'];
$countHelper= $_GET['countHelper'];
$idTrust= $_GET['tat_verantwortungslevel'];
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
	$sql='INSERT INTO Deeds (name,description, pictures, contactPerson,category,street,housenumber,postalcode,time,organization,countHelper,idTrust,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';
	$stmt = $db->prepare($sql);
	mysqli_stmt_bind_param($stmt, 'ssssssiiisiii' , $name, $description, $pictures, $contactPerson, $category,$street,$housenumber, $postalcode, $time_t,$organization,$countHelper,$idTrust,$status);
	$stmt->execute();
	$affected_rows = mysqli_stmt_affected_rows($stmt);
	echo '<h3>Ihre Tat wurde erfolgreich erstellt und wird nun von uns geprüft und freigegeben.</h3>';
	include './buttonsTatErstellen.html';
	if($affected_rows == 1) {	
	}
	else {
		echo '<h3>Interner Fehler: bitte kontaktieren Sie uns, falls Ihnen die Umstände des Fehlers nicht bekannt sind.</h3>'.mysqli_error($db);
	}
	}else{include './buttonsTatErstellen.html';}
//Ende einfügen der Tat	
	db_close($db);	





require './includes/_bottom.php';
?>