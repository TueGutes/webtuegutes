<?php
/*
*@authorLukas Buttke
*/

require './includes/DEF.php';
include './includes/ACCESS.php';
include './includes/db_connector.php';
require './includes/_top.php';

//profile_bewertung.php?user=testuser



?>

<?php

if(isset($_POST['user'])){
	$extraPoints = $_POST['bewertung'];
	$username  = $_POST['user'];
	$user = DBFunctions::db_idOfBenutzername($username);

	if(!is_null($user))
	{
		$thisuser = DBFunctions::db_get_user($username);
		$userPoint = $thisuser['points'];
		$userTrust = $thisuser['idTrust'];
		$points = $userPoint + $extraPoints;
		DBFunctions::db_userBewertung($points,$user);

		if($points <= 120)
		{
			$round = floor(($points/20));
			$trust = $round + 1;
			DBFunctions::db_userAnsehen($trust,$user);
		}

	}

	echo '<h3> Sie haben '.$username.' mit '.$extraPoints.' Punkten bewertet! </h>';
	echo '<h4> Mit steigender Punktzahl ('.$points.') steigt auch das Vertrauen ('.$trust.')</h>';
	echo '<hr> <a href="./profile?user='.$username.'"> <input type="Button" value="Zurück"> </a> ';

}
?>

<?php
require './includes/_bottom.php';
?>