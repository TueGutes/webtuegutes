<?php
/*
*@author Lukas Buttke
*/

include './includes/ACCESS.php';
include './includes/db_connector.php';
require './includes/_top.php';

function db_getGuteTat($idGuteTat){
	$db = db_connect();
	$sql = 'SELECT deeds.name, user.username, usertexts.avatar deeds.category, deeds.street, deeds.housenumber, deeds.postalcode deeds.time, deeds.organization, deeds.countHelper, trust.idTrust, trust.trustleveldescription
	FROM Deeds 
		Join User
			On (deeds.contactPerson = user.idUser)
		Join Usertexts
			On (user.idUser = usertexts.idUserTexts)
		Join Trust
			On (deeds.idTrust =	trust.idTrust)
	WHERE idGuteTat = 1';
	//$stmt = $db->prepare($sql);
	//$stmt->bind_param('i',$idGuteTat);
	//$stmt->execute();
	$result = $db->query($sql);
	$dbentry = $result->fetch_assoc();
	db_close($db);
	return $dbentry;
}

$idTat = 4;//$_GET["id"]; 
$tat_arr = db_getGuteTat($idTat);
?>

<?php

foreach($tat_arr AS $out) {
  echo "<br> $out";
}
count($tat_arr)

?>

<?php
require './includes/_bottom.php';
?>