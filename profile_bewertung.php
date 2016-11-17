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

$user = DBFunctions::db_getUserIDByUsername($username);

if(!is_null($user))
{

	// Momentan überschreibt er dummerweise die alten Points mit den Neuen
	// weil er es nicht schafft, die alten points auszulesen ...

	$thisuser = DBFunctions::db_get_user($user);
	$userPoint = $thisuser['points'];
	$points = $userPoint + $extraPoints;
	DBFunctions::db_userBewertung($points,$user);

}
echo '<h2> Sie haben '.$username.' mit '.$extraPoints.' Punkten bewertet! </h>';
echo '<hr> <a href="./profile?user='.$username.'"> <input type="Button" value="Zurück"> </a> ';
?>

<?php
require './includes/_bottom.php';
?>