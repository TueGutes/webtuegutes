<?php
/**
 * SicherheitsCheck
 * eine klasse die alle Sicherheitslücken schlissen soll
 *
 * @author Klaus Sobotta
 */
?>

<?php
/*XSS*/
function htmlString($string){
	return htmlspecialchars($string , ENT_QUOTES , 'UTF-8' ) ;
}
?>

<?php
function isUserExist($userid){
	if(!DBFunctions::db_doesUserExistInLoginCheck($userid)){
		DBFunctions::db_insertUserIntoLoginCheck($userid);
	}
}

/*checkt ob 15 min vergangen sind*/
function getTimeDifVon15($userid){
	if(DBFunctions::db_getTimeLoginCheck($userid)-15=jetztTime($userid)){
		DBFunctions::db_setCountandTimenull($userid)
	}
}
/*brute force*/
/*liefert true wenn man nochmal das passwort eingeben kann*/
/*ansonsten stehet da bitte versuchen sie es in min nochmal*/
function isCountUnter5($userid){
	if(DBFunctions::db_getCountLoginCheck($userid)<5){
		DBFunctions::db_setCountandTime($userid);
		return true;
	}
	return false;
}
function checkBruFo($userid){
	isUserExist($userid);
	getTimeDifVon15($userid);
	if(isCountUnter5){
		return true;
	}
	return false;
}
?>

<?php
/*eindeutige account erstellung*/
/*Beim öffnen der regestriren seite wird ein key in die datenbank gespeichert*/
/*dieser key muss dann beime einloggen übergeben werden */
/*perfekt wäre wenn der key nach 2 min gelöscht wird*/
function neuerAcount(){
	return DBFunctions::initNeuerKey();
}
?>