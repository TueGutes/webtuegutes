<?php 

/*
* @author: Timm Romanik
*/

//Definition der Datenbankverbindung
DEFINE('DB_USER','root');
DEFINE('DB_PASSWORD','');
DEFINE('DB_HOST','localhost');
DEFINE('DB_NAME','tueGutes');

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
function db_idOfBenutzername($benutzername) {
	$db = db_connect();
	$sql = "SELECT idUser FROM User WHERE username = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('s',$benutzername);
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
	
	//return false; //Testzwecke
}

//Gibt das Attribut idBenutzer zu einer gegebenen email Adresse zurück oder false,
//falls es keinen Acoount mit dieser Emailadresse gibt
function db_idOfEmailAdresse($emailadresse) {
	$db = db_connect();
	$sql = "SELECT idUser FROM User WHERE email = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('s',$emailadresse);
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
	
	//return false; //Testzwecke
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
function db_get_user($user) {
	$db = db_connect();
	$sql = "
		SELECT idUser, username, email, regDate, points, trustleveldescription, groupDescription, privacykey, avatar, hobbys, description, firstname, lastname, gender, street, housenumber, Postalcode.postalcode, place, telefonnumber, messengernumber, birthday 
		FROM User
			JOIN Trust
		    	ON User.idTrust = Trust.idTrust
			JOIN UserGroup
		    	ON User.idUserGroup = UserGroup.idUserGroup
			JOIN Privacy
		    	ON User.idUser = Privacy.idPrivacy
			JOIN UserTexts
		    	ON User.idUser = UserTexts.idUserTexts
			JOIN PersData
		    	ON User.idUser = PersData.idPersData
			JOIN Postalcode
				ON PersData.postalcode = Postalcode.postalcode
		WHERE username = ?";
	$stmt = $db->prepare($sql); 
	$stmt->bind_param('s',$user);
	$stmt->execute();
	$result = $stmt->get_result();
	db_close($db);
	return $result->fetch_assoc();
}

/*
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
*/

//Soll die Benutzerdaten abspeichern, die von Alex verlangt wurden
function db_update_user($savedata){
	$db = db_connect();
	$sql ="UPDATE User,PersData,UserTexts,Privacy,UserGroup
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
		PersData.postalcode = ?,
		UserTexts.avatar = ?,
		UserTexts.hobbys = ?,
		UserTexts.description = ?,
		Privacy.privacykey = ?
		WHERE User.idUser = ? 
		AND PersData.idPersData = User.idUser
		AND UserTexts.idUserTexts = User.idUser
		AND User.idUser = Privacy.idPrivacy
		AND User.idUserGroup = UserGroup.idUserGroup";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('sssssssssssssssi',
		$savedata['username'],
		$savedata['email'],
		$savedata['regDate'],
		$savedata['firstname'],
		$savedata['lastname'],
		$savedata['birthday'],
		$savedata['street'],
		$savedata['housenumber'],
		$savedata['telefonnumber'],
		$savedata['messengernumber'],
		$savedata['postalcode'],
		$savedata['avatar'],
		$savedata['hobbys'],
		$savedata['description'],
		$savedata['privacykey'],
		$savedata['idUser']);
	$stmt->execute();
	db_close($db);
}
/*
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
*/

/*Erstellt einen Benutzeraccount mit den angegeben Parametern, der Status ist erste einmal "unverifiziert*/
/*Liefert einen cryptkey, falls das Erstellen erfolgreich war, false falls nicht*/
function db_createBenutzerAccount($benutzername, $vorname, $nachname, $email, $passwort) {
	//TODO: Datenbank Insert ausarbeiten
	$db = db_connect();
	$sql = "Insert into User (username, password, email, regDate, points, status) values(?,?,?,?,0,'nichtVerifiziert')";
	$stmt = $db->prepare($sql);
	$date = date("Y-m-d");
	$pass_md5 = md5($passwort.$date);
	mysqli_stmt_bind_param($stmt, "ssss", $benutzername, $pass_md5, $email,$date);
	$stmt->execute();
	$affected_rows = mysqli_stmt_affected_rows($stmt);
	if($affected_rows == 1) {
		//return true;	
	}
	else {
		echo 'beim erstellen des nutzers ist was schief gegangen '.mysqli_error($db);
		//return false;
	}
	
	$sql = "Insert into Privacy (idPrivacy, privacykey, cryptkey) values ((SELECT MAX(idUser) FROM User),?,?)";
	$stmt = $db->prepare($sql);
	
	$cryptkey = md5($benutzername.$date); //Der Cryptkey wird erstellt
	$privacykey = "1111111111111"; //TODO: Privacykey richtig machen
	mysqli_stmt_bind_param($stmt, "ss", $privacykey, $cryptkey);
	$stmt->execute();
	$affected_rows = mysqli_stmt_affected_rows($stmt);
	if($affected_rows == 1) {
		//return true;	
	}
	else {
		echo 'beim erstellen des privacys ist was schief gegangen: '.mysqli_error($db);
		return false;
	}
	
	$sql = "Insert into PersData (idPersData, firstname, lastname) values((SELECT MAX(idUser) FROM User),?,?)";
	$stmt = $db->prepare($sql);
	mysqli_stmt_bind_param($stmt, "ss", $vorname, $nachname);
	$stmt->execute();
	$affected_rows = mysqli_stmt_affected_rows($stmt);
	if($affected_rows == 1) {
		//return true;	
	}
	else {
		echo 'beim erstellen von PersData Eintrag ist was schief gegangen '.mysqli_error($db);
		return false;
	}
	
	db_close($db);	
	
	return $cryptkey;
	
	//return "asdfjklö"; //Für Testzwecke
}

/*Setzt den Status des zum cryptkey gehörenden Accounts auf "verifiziert"*/
function db_activateAcount($cryptkey) {
	$db = db_connect();
	$sql = "UPDATE User SET status = 'Verifiziert' WHERE idUser = (SELECT idPrivacy FROM Privacy WHERE cryptkey = ?)";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('s',$cryptkey);
	$stmt->execute();
	//$result = $stmt->get_result();
	//$dbentry = $result->fetch_assoc();
	db_close($db);				
	//if(isset($dbentry['idUser'])){
	//	return $dbentry['idUser'];
	//}
	//else {
	//	return false;
	//}
	//Verfiziert
	return true;
}

/*Liefert den Benutzernamen des Accounts, der zum cryptkey gehört oder false*/
function db_getUserByCryptkey($cryptkey) {
	$db = db_connect();
	$sql = "SELECT username FROM User WHERE idUser = (SELECT idPrivacy FROM Privacy WHERE cryptkey = ?)";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('s',$cryptkey);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);				
	if(isset($dbentry['username'])){
		return $dbentry['username'];
	}
	else {
		return false;
	}
	//return "blecha"; //Testzwecke
}

?>
