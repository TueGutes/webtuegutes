<?php
/*
*@authorLukas Buttke
*/

require './includes/DEF.php';
include './includes/ACCESS.php';
include './includes/db_connector.php';
require './includes/_top.php';

?>

<?php

$extraPoints = $_POST['bewertung'];
$username  = $_POST['user'];
$user = DBFunctions::db_idOfBenutzername($username);

if(!is_null($user))
{

	$thisuser = DBFunctions::db_get_user($username);
	$userPoint = $thisuser['points'];
	$points = $userPoint + $extraPoints;
	DBFunctions::db_userBewertung($points,$user);

	$userTrust = $thisuser['idTrust'];
	if((($points % 10) == 0)&&($points <= 60))
	{
		$trust = $userTrust + 1;
		DBFunctions::db_userAnsehen($trust,$user);
		echo "<h4> Trustlevel aufgestiegen ! </h>";
	}
}
echo '<h2> Sie haben '.$username.' mit '.$extraPoints.' Punkten bewertet! </h>';
echo '<hr> <a href="./profile?user='.$username.'"> <input type="Button" value="Zurück"> </a> ';
?>

<?php
require './includes/_bottom.php';
?>