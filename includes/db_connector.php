<?php 

/*
* @author: Timm Romanik
*/

//Definition der Datenbankverbindung
DEFINE('DB_USER','tueGutes');
DEFINE('DB_PASSWORD','Sadi23n2os');
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


//Gibt alle Daten einer Guten tat zu einer bestimmten Id der guten Tat aus.
function db_getGuteTatbyid($idvonGuteTat){
	$db = db_connect();
	$sql = "SELECT * FROM Deeds WHERE idguteTat = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$idvonGuteTat);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);
	return $dbentry;

}


/*Erstellt einen Benutzeraccount mit den angegeben Parametern, der Status ist erste einmal "unverifiziert*/
/*Liefert einen cryptkey, falls das Erstellen erfolgreich war, false falls nicht*/
function db_createBenutzerAccount($benutzername, $vorname, $nachname, $email, $passwort) {
	//TODO: Datenbank Insert ausarbeiten
	$db = db_connect();
	$sql = "INSERT INTO User (username, password, email, regDate, points, status) VALUES(?,?,?,?,0,'nichtVerifiziert')";
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
	
	$sql = "INSERT INTO Privacy (idPrivacy, privacykey, cryptkey) VALUES ((SELECT MAX(idUser) FROM User),?,?)";
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
	
	$sql = "INSERT INTO PersData (idPersData, firstname, lastname) VALUES((SELECT MAX(idUser) FROM User),?,?)";
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

//füllt den Ort als Unbekannt, wenn eine Neue Postleitzahl eingefügt wird
function db_fix_plz($plz) {
	$db = db_connect();
	$sql = "SELECT * from Postalcode where postalcode = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$plz);
	$stmt->execute();
	$result = $stmt->get_result();
	if (!isset($result->fetch_assoc['postalcode'])) {
		$sql = 'INSERT INTO Postalcode (postalcode, place) VALUES (?, "Unbekannt")';
		$stmt = $db->prepare($sql);
		$stmt->bind_param('i',$plz);
		$stmt->execute();
	}
	db_close($db);
}

function db_get_user($user) {
	$db = db_connect();
	$sql = "
		SELECT idUser, password, username, email, regDate, points, trustleveldescription, groupDescription, privacykey, avatar, hobbys, description, firstname, lastname, gender, street, housenumber, postalcode, telefonnumber, messengernumber, birthday 
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
		WHERE username = ?";
	$stmt = $db->prepare($sql); 
	$stmt->bind_param('s',$user);
	$stmt->execute();
	$result = $stmt->get_result();
	$thisuser = $result->fetch_assoc();
	if (isset($thisuser['postalcode'])) {
		//PLZ gesetzt -> Lade den Namen des Ortes aus der Datenbank
		$sql = "
			SELECT postalcode, place 
			FROM Postalcode
			WHERE postalcode = ?
		";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('s',$thisuser['postalcode']);
		$stmt->execute();
		$result = $stmt->get_result();
		$dbentry = $result->fetch_assoc();
		$thisuser['place'] = $dbentry['place'];
	} else {
		$thisuser['postalcode'] = '';
		$thisuser['place'] = '';
	}
	//Schließen der Datenbankverbindung
	db_close($db);

	//Setzen von Eigenschaften, die nicht aus der Datenbank geladen werden konnten
	if (!isset($thisuser['avatar'])) $thisuser['avatar'] = "";
	if (!isset($thisuser['hobbys'])) $thisuser['hobbys'] = "";
	if (!isset($thisuser['description'])) $thisuser['description'] = "";
	if (!isset($thisuser['gender'])) $thisuser['gender'] = "";
	if (!isset($thisuser['street'])) $thisuser['street'] = "";
	if (!isset($thisuser['housenumber'])) $thisuser['housenumber'] = "";
	if (!isset($thisuser['telefonnumber'])) $thisuser['telefonnumber'] = "";
	if (!isset($thisuser['messengernumber'])) $thisuser['messengernumber'] = "";
	if (!isset($thisuser['birthday'])) $thisuser['birthday'] = "";
	
	return $thisuser;

}

//Soll die Benutzerdaten abspeichern, die von Alex verlangt wurden
function db_update_user($savedata)
{
	db_fix_plz($savedata['postalcode']);
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
	if (!$stmt->execute()) {
		die('Fehler: ' . mysqli_error($db));
	}
	db_close($db);
}

//Löscht alle informationen zu einem User
function db_delete_user($user, $pass) {
	$me = db_get_user($user);
	$pass_md5 = md5($pass.substr($me['regDate'],0,10));
	if ($pass_md5 === $me['password']) {
		$db = db_connect();

		$sql = "DELETE FROM PersData WHERE idPersData= ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('i',$me['idUser']);
		$stmt->execute();

		$sql = "DELETE FROM UserTexts WHERE idUserTexts= ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('i',$me['idUser']);
		$stmt->execute();

		$sql = "DELETE FROM Privacy WHERE idPrivacy= ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('i',$me['idUser']);
		$stmt->execute();

		$sql = "DELETE FROM User WHERE idUser = ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('i',$me['idUser']);
		$stmt->execute();

		Header('Location:./logout.php');
	} else {
		die ('RegDate: ' . substr($me['regDate'],0,10) . 'DB: ' . $me['password'] . '<br>Eingegeben: ' . $pass_md5);
	}
}


// Holt sich eine Gute Tat und zusätzliche Parameter, die von Lukas gefordert waren.
function db_getGuteTat($idGuteTat){
	$db = db_connect();
	$sql = "SELECT 
		Deeds.name, 
		User.username, 
		UserTexts.avatar,
		Deeds.category, 
		Deeds.street, 
		Deeds.housenumber, 
		Deeds.postalcode,
		Deeds.time, 
		Deeds.organization, 
		Deeds.countHelper, 
		Deeds.status,
		Trust.idTrust, 
		Trust.trustleveldescription,
		DeedTexts.description,
		DeedTexts.pictures
	FROM Deeds 
		Join User
			On (Deeds.contactPerson = User.idUser)
		Join UserTexts
			On (User.idUser = UserTexts.idUserTexts)
		Join Trust
			On (Deeds.idTrust =	Trust.idTrust)
		Join DeedTexts
			On (Deeds.idGuteTat = DeedTexts.idDeedTexts)			
	WHERE idGuteTat = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$idGuteTat);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);
	return $dbentry;
}

//gibt die nötigen Parameter die für die liste gefordert wurden aus
//man kann bestimmen ab welcher id die Taten angezeigt werden und wie viele
//Taten werden als Objekt in dem Array gespeichert
function db_getGuteTatenForList($startrow,$numberofrows){
	$db = db_connect();
	$sql = "SELECT 
		Deeds.name, 
		Deeds.category, 
		Deeds.street, 
		Deeds.housenumber, 
		Deeds.postalcode,
		Deeds.organization, 
		Deeds.countHelper,
		Deeds.status, 
		Trust.idTrust, 
		Trust.trustleveldescription,
		DeedTexts.description,
		Postalcode.place
	FROM Deeds 
		Join DeedTexts
			On (Deeds.idGuteTat = DeedTexts.idDeedTexts)
		Join Postalcode
			On (Deeds.postalcode = Postalcode.postalcode)
		Join Trust
			On (Deeds.idTrust =	Trust.idTrust)
	LIMIT ? , ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('ii',$startrow,$numberofrows);
	$stmt->execute();
	$result = $stmt->get_result();
	db_close($db);
	$arr = array();
	while($dbentry =$result->fetch_object()){
		$arr[]= $dbentry;
	}
	return $arr;

}

// Liefert True wenn der Name schon existiert, sonst false
function db_doesGuteTatNameExists($name){
	$db = db_connect();
	$sql = "SELECT name FROM Deeds WHERE name = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('s',$name);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);
	if(isset($dbentry['name'])){
		return true;
	}
	else {
		return false;
	}
}

//erstellt eine gute Tat
function db_createGuteTat($name, $user_id, $category, $street, $housenumber, $postalcode, $time_t, $organization, $countHelper, $idTrust,
	$description, $pictures){
	$db = db_connect();
	//Datensatz in Deeds einfügen
	$sql='INSERT INTO Deeds (name, contactPerson, category,street,housenumber,postalcode,time,organization,countHelper,idTrust) VALUES (?,?,?,?,?,?,?,?,?,?)';
	$stmt = $db->prepare($sql);
	$stmt->bind_param('sisssissii', $name, $user_id, $category, $street, 
		$housenumber, $postalcode, $time_t, $organization, $countHelper, 
		$idTrust);
	$stmt->execute();

	//Herausfinden der größten ID von Guten taten
	$sql = 'SELECT MAX(idGuteTat) AS "index" FROM Deeds';
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$result = $stmt->get_result()->fetch_assoc();
	if (isset($result['index'])) {
		$index = $result['index'];
	} else {
		$index = 0;
	}

	//Einfügen der DeedsTexts mit der passenden ID zu der neuen Guten Tat
	$sql='INSERT INTO DeedTexts (idDeedTexts, description, pictures) VALUES (?,?,?)';
	$stmt = $db->prepare($sql);
	$stmt->bind_param('iss' , $index, $description, $pictures);
	$stmt->execute();
	db_close($db);
}

?>
