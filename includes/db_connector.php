<?php 

//Definition der Datenbankverbindung
DEFINE('DB_USER','root');
DEFINE('DB_PASSWORD','');
DEFINE('DB_HOST','localhost');
DEFINE('DB_NAME','tuegutesdb');


// öffnet eine DB Verbindung mit den definierten Parametern
function db_connect() {
	return mysqli_connect(DB_HOST,DB_USER, DB_PASSWORD, DB_NAME);
}

// schließt eine offene Datenbankverbindung
function db_close(mysqli $db) {
	mysqli_close($db);
}

//Gibt das Attribut idBenutzer zu einem gegebenen Benutzernamen zurück oder false,
//falls es keinen Account mit dem Benutzernamen gibt
function db_checkUsername($username) {
    $db = db_connect();
    $sql = "SELECT idUser FROM User WHERE username = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s',$username);
    $stmt->execute();
    $result = $stmt->get_result();
    $dbentry = $result->fetch_assoc();
    db_close($db);
    if(isset($dbentry['idUser'])){
        return $dbentry['idUser'];
    }
    else {
        return false;
    }
}

//Gibt das Attribut idBenutzer zu einer gegebenen email Adresse zurück oder false,
//falls es keinen Acoount mit dieser Emailadresse gibt
function db_checkEmail($emailadress){
	$db = db_connect();
	$sql = "SELECT idUser FROM User WHERE email = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('s',$emailadress);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);
	if(isset($dbentry['idUser'])){
 		    return $dbentry['idUser'];
 	}
 	else {
 		return false;
 	}
}

//Gibt die Anzahl der Benutzer zurück, egal welcher Status sie innehaben
function db_getBenutzerAnzahl(){
	$db = db_connect();
	$sql = "SELECT COUNT(*) FROM User";
	$result = $db->query($sql);
	$dbentry = $result->fetch_assoc();
	db_close($db);
	return $dbentry['COUNT(*)'];
}


//gibt die Gute Taten als Objekte in einem Array zurück
function db_getGuteTaten(){
	$db = db_connect();
	$sql = "SELECT * FROM Deeds";
	$result = $db->query($sql);
	db_close($db);
	$arr = array();
	while($dbentry =$result->fetch_object()){
		$arr[]= $dbentry();
	}
	return $arr;
}

// Zeigt die Daten eines Benutzer an, die in dem Backlog gefordert worden sind
function db_getUserData($UserID){
	$db = db_connect();
	$sql = "SELECT
		User.idUser
  		User.username,
  		User.email,
  		User.regDate,
  		PersData.firstname,
		PersData.lastname,
		PersData.birthday,
		PersData.street,
		PersData.housenumber,
		PersData.telefonnumber,
		PersData.messengernumber,
		UserTexts.avatar,
		UserTexts.hobbys,
		UserTexts.description
		FROM UserTexts
  			INNER JOIN User
    			ON UserTexts.idUserTexts = User.idUser
  			INNER JOIN PersData
    			ON PersData.idPersData = User.idUser
		WHERE User.idUser = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$UserID);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);
	return $dbentry;
}


//Soll die Benutzerdaten abspeichern, die von Alex verlangt wurden
function db_saveUserData($savedata){
	$db = db_connect();
	$sql ="UPDATE User,PersData,UserTexts
		SET 
		User.username = ?,
		User.email = ?,
		User.regDate = ?,
		PersData.firstname = ?,
		PersData.lastname = ?,
		PersData.birthday = ?,
		PersData.street = ?,
		PersData.housenumber = ?,
		PersData.telefonnumber = ?,
		PersData.messengernumber = ?,
		UserTexts.avatar = ?,
		UserTexts.hobbys = ?,
		UserTexts.description = ?
		WHERE User.idUser = ? 
		AND PersData.idPersData = User.idUser
		AND UserTexts.idUserTexts = User.idUser;"
	$stmt = $db->prepare($sql);
	$stmt->bind_param('sssssssssssssi',
		$savedata['User.username'],
		$savedata['User.email'],
		$savedata['User.regDate'],
		$savedata['PersData.firstname'],
		$savedata['PersData.lastname'],
		$savedata['PersData.birthday'],
		$savedata['PersData.street'],
		$savedata['PersData.housenumber'],
		$savedata['PersData.telefonnumber'],
		$savedata['PersData.messengernumber'],
		$savedata['UserTexts.avatar'],
		$savedata['UserTexts.hobbys'],
		$savedata['UserTexts.description'],
		$savedata['User.idUser']);
	$stmt->execute();
	db_close($db);
}
?>
