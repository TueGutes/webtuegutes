<?php

//Server Definitions
$ABSOLUE_PATH = "/";
$HOSTNAME = $_SERVER['HTTP_HOST'];
$HOST_FULL = "http" . (isset($_SERVER['HTTPS']) ? "s" : "") . "://" . $HOSTNAME . $_SERVER['REQUEST_URI'];
$HOST = substr($HOST_FULL, 0, strrpos($HOST_FULL, "/"));

//Includes
require './includes/db_connector.php';
require './includes/mail.php';

wrap_db_connector($_POST);

function wrap_db_connector($parameters) {
$user_id = @$parameters['user_id'];
$function_name = @$parameters['function_name'];
	if (!isset($user_id) || !isset($function_name) /*|| db_isUserBanned($user_id)*/) {
		echo json_encode(array('success'=>false));
		exit;
	}

	switch ($function_name) {
		case "login_user":
			echo json_encode(DBFunctions::db_validateCredentials($parameters['username'], $parameters['password']));
			break;
		case "db_createGuteTat":
			$pid = DBFunctions::db_getIdPostalbyPostalcodePlace($parameters['postalcode'], $parameters['place']);
			//Wenn die PLZ/Ort Kombination nicht existiert, füge sie hinzu!
			if($pid == "")
			{
				DBFunctions::db_insertPostalCode($parameters['postalcode'], $parameters['place']);
				$pid = DBFunctions::db_getIdPostalbyPostalcodePlace($parameters['postalcode'], $parameters['place']);
			}
			echo json_encode(DBFunctions::db_createGuteTat($parameters['$name'],$parameters['$user_id'],$parameters['$category'],$parameters['$street'],$parameters['$housenumber'],$pid,$parameters['$starttime'],$parameters['$endtime'],$parameters['$organization'],$parameters['$countHelper'],$parameters['$idTrust'],$parameters['$description'],$parameters['$pictures'],$parameters['$flag']));
			break;
		case "db_getGuteTaten":
			echo json_encode(DBFunctions::db_getGuteTatenForList($parameters['start'], $parameters['entries'], 'alle'));
			//echo json_encode(DBFunctions::db_getGuteTaten());
			break;
		case "db_getGuteTatenByUserId":
			echo json_encode(DBFunctions::db_getGuteTatenByUserId($parameters['user_id'],'alle'));
			break;
		case "db_countGuteTaten":
			echo json_encode(DBFunctions::db_getGuteTatenAnzahl('alle'));
			break;
		case  "db_regDateOfUserID":
			$retVal = DBFunctions::db_regDateOfUserID($parameters['user_id']);
			if ($retVal == false)
				echo json_encode( null );
			else
				echo json_encode($retVal);
			break;
		case  "db_passwordHashOfUserID":
			$retVal = DBFunctions::db_passwordHashOfUserID($parameters['user_id']);
			if ($retVal == false)
				echo json_encode( null );
			else
				echo json_encode($retVal);
			break;
		case  "db_statusByUserID":
			$retVal = DBFunctions::db_statusByUserID($parameters['user_id']);
			if ($retVal == false)
				echo json_encode( null );
			else
				echo json_encode( $retVal );
			break;
		case "db_idOfBenutzername":
			$retVal = DBFunctions::db_idOfBenutzername($parameters['benutzername']);
			if ($retVal == false)
				echo json_encode( -1 );
			else
				echo json_encode( $retVal );
			break;
		case "db_get_user":
			$retVal = DBFunctions::db_get_user($parameters['benutzername']);
			if ($retVal == false)
				echo json_encode( null );
			else
				echo json_encode($retVal);
			break;
		case "db_getApplicationsByUserID":
			echo json_encode(DBFunctions::db_getApplicationsByUserID($parameters['user_id']));
			break;
		case "db_getApplicationsByDeedsID":
			echo json_encode(DBFunctions::db_getApplicationsByDeedsID($parameters['$idGuteTat']));
			break;
		case "db_deleteDeed":
			DBFunctions::db_deleteDeed($parameters['idGuteTat']);
			echo json_encode("Tat geloescht");
			break;
		case "deed_addBewerbungAndSendEmail":
			//Add Bewerbung
			DBFunctions::db_addBewerbung($parameters['user_id'], $parameters['idGuteTat'], $parameters['bewerbungstext']);
			//Send Email
			$receiver = DBFunctions::db_getEmailOfContactPersonByGuteTatID($parameters['idGuteTat']);
			$subject = "Neue Bewerbung für " . DBFunctions::db_getNameOfGuteTatByID($parameters['idGuteTat']);
			$mailtext = applicationForDeed(
						$parameters['idGuteTat'], 
						$parameters['user_id'], 
						DBFunctions::db_getUsernameOfContactPersonByGuteTatID($parameters['idGuteTat']),
						DBFunctions::db_getUsernameOfBenutzerByID($parameters['user_id']), 
						DBFunctions::db_getNameOfGuteTatByID($parameters['idGuteTat']), 
						$parameters['bewerbungstext']
			);
			sendEmail($receiver, $subject, $mailtext);
			break;
		default:
			return -315;
			break;
	}
}

function applicationForDeed($idGuteTat, $idUser, $UsernameOfErsteller, $UsernameOfBewerber, $NameOfGuteTat, $Bewerbungstext) {
	global $HOST;
	$actual_link = $HOST . "/deeds_bewerbung"."?idGuteTat=$idGuteTat&candidateID=$idUser";

	return "<div style=\"margin-left:10%;margin-right:10%;background-color:#757575\">
			<img src=\"img/wLogo.png\" alt=\"TueGutes\" title=\"TueGutes\" style=\"width:25%\"/>
		</div>
		<h2>Hallo $UsernameOfErsteller!</h2><br>
		<h3>$UsernameOfBewerber hat sich für deine gute Tat '$NameOfGuteTat' beworben. <br>
		<h3>Er schreibt dazu: \"" . $Bewerbungstext . "\"</h3><br>
		<h3>Besuche die <a href=\"$actual_link\">URL, um Details zur Bewerbung einzusehen</a></h3>";
}

?>
