<?php

require './includes/db_connector.php';

wrap_db_connector($_POST);

function wrap_db_connector($parameters) {
$user_id = @$parameters['user_id'];
$function_name = @$parameters['function_name'];
	if (!isset($user_id) || !isset($function_name) /*|| db_isUserBanned($user_id)*/) {
		echo json_encode(array('success'=>false));
		exit;
	}

	switch ($function_name) {
		case "db_getGuteTaten":
			echo json_encode( DBFunctions::db_getGuteTaten());
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
		default:
			return -315;
			break;
	}
}

?>
