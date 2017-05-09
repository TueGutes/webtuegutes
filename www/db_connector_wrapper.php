<?php

require './includes/db_connector.php';

wrap_db_connector($_POST);

function wrap_db_connector($parameters) {
$user_id = @$parameters['user_id'];
$function_name = @$parameters['function_name'];
	if (!isset($user_id) || !isset($function_name) /*|| db_isUserBanned($user_id)*/) {
		echo json_encode(array('success'=>false));exit;
	}

	switch ($function_name) {
		case  "db_connect":
			echo json_encode( DBFunctions::db_connect($parameters));
			break;
		case  "db_close":
			echo json_encode( db_close($parameters));
			break;
		case  "db_idOfBenutzername":
			echo json_encode( db_idOfBenutzername($parameters));
			break;
		case  "db_idOfEmailAdresse":
			echo json_encode( db_idOfEmailAdresse($parameters));
			break;
		case  "db_getBenutzerAnzahl":
			echo json_encode( DBFunctions::db_getBenutzerAnzahl($parameters));
			break;
		case  "db_getGuteTaten":
			echo json_encode( DBFunctions::db_getGuteTaten($parameters));
			break;
		case  db_getGuteTatbyid:
			echo json_encode( db_getGuteTatbyid($parameters));
			break;
		case  db_createBenutzerAccount:
			echo json_encode( db_createBenutzerAccount($parameters));
			break;
		case  db_createOverFBBenutzerAccount:
			echo json_encode( db_createOverFBBenutzerAccount($parameters));
			break;
		case  db_getUserIDbyFacebookID:
			echo json_encode( db_getUserIDbyFacebookID($parameters));
			break;
		case  db_doesFacebookUserExists:
			echo json_encode( db_doesFacebookUserExists($parameters));
			break;
		case  db_activateAccount:
			echo json_encode( db_activateAccount($parameters));
			break;
		case  db_getUserByCryptkey:
			echo json_encode( db_getUserByCryptkey($parameters));
			break;
		case  db_fix_plz:
			echo json_encode( db_fix_plz($parameters));
			break;
		case  db_getIdPostalbyPostalcodePlace:
			echo json_encode( db_getIdPostalbyPostalcodePlace($parameters));
			break;
		case  db_getPostalcodePlacebyIdPostal:
			echo json_encode( db_getPostalcodePlacebyIdPostal($parameters));
			break;
		case  db_get_user:
			echo json_encode( db_get_user($parameters));
			break;
		case  db_update_user:
			echo json_encode( db_update_user($parameters));
			break;
		case  db_delete_user:
			echo json_encode( db_delete_user($parameters));
			break;
		case  db_delete_user_v2:
			echo json_encode( db_delete_user_v2($parameters));
			break;
		case  db_getGuteTat:
			echo json_encode( db_getGuteTat($parameters));
			break;
		case  db_doesGuteTatNameExists:
			echo json_encode( db_doesGuteTatNameExists($parameters));
			break;
		case  db_createGuteTat:
			echo json_encode( db_createGuteTat($parameters));
			break;
		case  db_getGuteTatenForList:
			echo json_encode( db_getGuteTatenForList($parameters));
			break;
		case  db_getGuteTatenAnzahl:
			echo json_encode( db_getGuteTatenAnzahl($parameters));
			break;
		case  "db_regDateOfUserID":
			echo json_encode( DBFunctions::db_regDateOfUserID($parameters));
			break;
		case  "db_passwordHashOfUserID":
			echo json_encode( DBFunctions::db_passwordHashOfUserID($parameters));
			break;
		case  "db_statusByUserID":
			echo json_encode( DBFunctions::db_statusByUserID($parameters));
			break;
		case  db_doesGuteTatExists:
			echo json_encode( db_doesGuteTatExists($parameters));
			break;
		case  db_isUserCandidateOfGuteTat:
			echo json_encode( db_isUserCandidateOfGuteTat($parameters));
			break;
		case  db_getUserIdOfContactPersonByGuteTatID:
			echo json_encode( db_getUserIdOfContactPersonByGuteTatID($parameters));
			break;
		case  db_getStatusOfGuteTatById:
			echo json_encode( db_getStatusOfGuteTatById($parameters));
			break;
		case  db_isNumberOfAcceptedCandidatsEqualToRequestedHelpers:
			echo json_encode( db_isNumberOfAcceptedCandidatsEqualToRequestedHelpers($parameters));
			break;
		case  db_getStatusOfBewerbung:
			echo json_encode( db_getStatusOfBewerbung($parameters));
			break;
		case  db_getMailOfBenutzerByID:
			echo json_encode( db_getMailOfBenutzerByID($parameters));
			break;
		case  db_getNameOfGuteTatByID:
			echo json_encode( db_getNameOfGuteTatByID($parameters));
			break;
		case  db_getUsernameOfContactPersonByGuteTatID:
			echo json_encode( db_getUsernameOfContactPersonByGuteTatID($parameters));
			break;
		case  db_getEmailOfContactPersonByGuteTatID:
			echo json_encode( db_getEmailOfContactPersonByGuteTatID($parameters));
			break;
		case  db_getUsernameOfBenutzerByID:
			echo json_encode( db_getUsernameOfBenutzerByID($parameters));
			break;
		case  db_addBewerbung:
			echo json_encode( db_addBewerbung($parameters));
			break;
		case  db_acceptBewerbung:
			echo json_encode( db_acceptBewerbung($parameters));
			break;
		case  db_declineBewerbung:
			echo json_encode( db_declineBewerbung($parameters));
			break;
		case  db_getAllModerators:
			echo json_encode( db_getAllModerators($parameters));
			break;
		case  db_getAllAdministrators:
			echo json_encode( db_getAllAdministrators($parameters));
			break;
		case  db_getIDOfGuteTatbyName:
			echo json_encode( db_getIDOfGuteTatbyName($parameters));
			break;
		case  db_istFreigegeben:
			echo json_encode( db_istFreigegeben($parameters));
			break;
		case  db_guteTatFreigeben:
			echo json_encode( db_guteTatFreigeben($parameters));
			break;
		case  db_guteTatAblehnen:
			echo json_encode( db_guteTatAblehnen($parameters));
			break;
		case  db_getCryptkeyByMail:
			echo json_encode( db_getCryptkeyByMail($parameters));
			break;
		case  db_regDateByCryptkey:
			echo json_encode( db_regDateByCryptkey($parameters));
			break;
		case  db_changePasswortByCryptkey:
			echo json_encode( db_changePasswortByCryptkey($parameters));
			break;
		case  db_update_deeds_starttime:
			echo json_encode( db_update_deeds_starttime($parameters));
			break;
		case  db_update_deeds_endtime:
			echo json_encode( db_update_deeds_endtime($parameters));
			break;
		case  db_update_deeds_picture:
			echo json_encode( db_update_deeds_picture($parameters));
			break;
		case  db_update_deeds_description:
			echo json_encode( db_update_deeds_description($parameters));
			break;
		case  db_update_deeds_name:
			echo json_encode( db_update_deeds_name($parameters));
			break;
		case  db_update_deeds_category:
			echo json_encode( db_update_deeds_category($parameters));
			break;
		case  db_update_deeds_street:
			echo json_encode( db_update_deeds_street($parameters));
			break;
		case  db_update_deeds_housenumber:
			echo json_encode( db_update_deeds_housenumber($parameters));
			break;
		case  db_update_deeds_postalcode:
			echo json_encode( db_update_deeds_postalcode($parameters));
			break;
		case  db_update_deeds_organization:
			echo json_encode( db_update_deeds_organization($parameters));
			break;
		case  db_update_deeds_countHelper:
			echo json_encode( db_update_deeds_countHelper($parameters));
			break;
		case  db_update_deeds_idTrust:
			echo json_encode( db_update_deeds_idTrust($parameters));
			break;
		case  db_insertPostalCode:
			echo json_encode( db_insertPostalCode($parameters));
			break;
		case  db_guteTatClose:
			echo json_encode( db_guteTatClose($parameters));
			break;
		case  db_istGeschlossen:
			echo json_encode( db_istGeschlossen($parameters));
			break;
		case  db_getGuteTatenForUser:
			echo json_encode( db_getGuteTatenForUser($parameters));
			break;
		case  db_countGuteTatenForUser:
			echo json_encode( db_countGuteTatenForUser($parameters));
			break;
		case  db_userBewertung:
			echo json_encode( db_userBewertung($parameters));
			break;
		case  db_userAnsehen:
			echo json_encode( db_userAnsehen($parameters));
			break;
		case  db_getIdUserByUsername:
			echo json_encode( db_getIdUserByUsername($parameters));
			break;
		case  db_deleteDeed:
			echo json_encode( db_deleteDeed($parameters));
			break;
		case  db_searchDuringGutes:
			echo json_encode( db_searchDuringGutes($parameters));
			break;
		case  db_searchDruingUsername:
			echo json_encode( db_searchDruingUsername($parameters));
			break;
		case  db_searchDuringOrt:
			echo json_encode( db_searchDuringOrt($parameters));
			break;
		case  db_searchDuringZeit:
			echo json_encode( db_searchDuringZeit($parameters));
			break;
		case  db_set_sortBedingung:
			echo json_encode( db_set_sortBedingung($parameters));
			break;
		case  db_getCategorytextbyCategoryid:
			echo json_encode( db_getCategorytextbyCategoryid($parameters));
			break;
		case  db_getCategoryidbyCategoryText:
			echo json_encode( db_getCategoryidbyCategoryText($parameters));
			break;
		case  db_getAllCategories:
			echo json_encode( db_getAllCategories($parameters));
			break;
		case  db_insertNewCategory:
			echo json_encode( db_insertNewCategory($parameters));
			break;
		case  db_doesCategoryNameExist:
			echo json_encode( db_doesCategoryNameExist($parameters));
			break;
		case  db_doesCategoryIDExist:
			echo json_encode( db_doesCategoryIDExist($parameters));
			break;
		case  db_doesCommentwithIDExist:
			echo json_encode( db_doesCommentwithIDExist($parameters));
			break;
		case  db_doesCommentwithCreatoridExist:
			echo json_encode( db_doesCommentwithCreatoridExist($parameters));
			break;
		case  db_createDeedComment:
			echo json_encode( db_createDeedComment($parameters));
			break;
		case  db_createDeedCommentsToList:
			echo json_encode( db_createDeedCommentsToList($parameters));
			break;
		case  db_countDeedComments:
			echo json_encode( db_countDeedComments($parameters));
			break;
		case  db_getCountLoginCheck:
			echo json_encode( db_getCountLoginCheck($parameters));
			break;
		case  db_doesUserExistInLoginCheck:
			echo json_encode( db_doesUserExistInLoginCheck($parameters));
			break;
		case  db_setCountandTime:
			echo json_encode( db_setCountandTime($parameters));
			break;
		case  db_setCountandTimenull:
			echo json_encode( db_setCountandTimenull($parameters));
			break;
		case  db_getTimeLoginCheck:
			echo json_encode( db_getTimeLoginCheck($parameters));
			break;
		case  db_insertUserIntoLoginCheck:
			echo json_encode( db_insertUserIntoLoginCheck($parameters));
			break;
		case  db_initNewKey:
			echo json_encode( db_initNewKey($parameters));
			break;
		case  db_getKey:
			echo json_encode( db_getKey($parameters));
			break;
		case  db_deleteKey:
			echo json_encode( db_deleteKey($parameters));
			break;
		case  db_initpwNewKey:
			echo json_encode( db_initpwNewKey($parameters));
			break;
		case  db_getpwKey:
			echo json_encode( db_getpwKey($parameters));
			break;
		case  db_deletepwKey:
			echo json_encode( db_deletepwKey($parameters));
			break;
		default:
			return -315;
			break;
	}
}

?>
