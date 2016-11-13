<?php 

/**
* @author Timm Romanik <timm.romanik@stud.hs-hannover.de
*/

//Definition der Datenbankverbindung
DEFINE('DB_USER','tueGutes');
DEFINE('DB_PASSWORD','Sadi23n2os');
DEFINE('DB_HOST','localhost');
DEFINE('DB_NAME','tueGutes');


/**
*Öffnen einer Datenbank Verbindung.
*
*Öffnet eine Datenbank Verbindung mit Parametern die vorher fest definiert sind. Die Parameter sind Host, Benutzer, password und Datenbankname
*
*@return mysqli Datenbankverbindungsobjekt auf dem gearbeitet werden kann
*/
function db_connect() {
	return mysqli_connect(DB_HOST,DB_USER, DB_PASSWORD, DB_NAME);
}

/**
*Schließt eine Datenbankverbindung.
*
*Schließt eine Datenbankverbindung unter verwendung eines Parameters, was ein mysqli Objekt ist
*
*@param mysqli Datenbankverbindungsobjekt
*/
function db_close(mysqli $db) {
	mysqli_close($db);
}

/**
*Gibt Id von User zu einem Benutzernamen zurück.
*
*Gibt die Id von dem Benutzer zurück, dessen Name als Parameter in die Funktion übergeben wurde.
*
*@param String Benutzername
*
*@return Int Oder false wenn es den Benutzer nicht gibt
*/
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

/**
*Gibt Id von User zu einer Emailadresse zurück.
*
*Gibt die Id von dem Benutzer zurück, dessen Email als Parameter in die Funktion übergeben wurde.
*
*@param String Emailadresse
*
*@return Int Oder false wenn es den Benutzer nicht gibt
*/
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

/**
*Gibt die Anzahl der Benutzer zurück.
*
*Gibt die Anzahl der Benutzer zurück, egal welchen Status sie haben, ob verifiziert oder nicht.
*
*@return Int Anzahl
*/
function db_getBenutzerAnzahl(){
	$db = db_connect();
	$sql = "SELECT COUNT(*) FROM User";
	$result = $db->query($sql);
	$dbentry = $result->fetch_assoc();
	db_close($db);
	return $dbentry['COUNT(*)'];
}

/**
*Gibt die Anzahl der Guten Taten zurück.
*
*Gibt die Anzahl der Guten Tatenzurück, egal welchen Status sie haben, ob sie freigegeben,geschlossen  oder noch freigeschaltet werden müssen.
*
*@return object[] Gute Taten als Objekte in einem Array
*/
function db_getGuteTaten(){
	$db = db_connect();
	$sql = "SELECT * FROM Deeds";
	$result = $db->query($sql);
	db_close($db);
	$arr = array();
	while($dbentry =$result->fetch_object()){
		$arr[]= $dbentry;
	}
	return $arr;
}

/**
*Gibt die eine Guten Tat zurück.
*
*Gibt eine gute Tat zu ihrer ID zürück. Es werden alle Attribute der Guten Taten tabelle zurück gegeben.
*
*@param Int Id der guten Tat
*
*@return mixed[] Alle Attribute der Guten Taten Tabelle in einem Array als Werte
*/
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

/**
*Erstellt einen Benutzer.
*
*Erstellt einen Benutzeraccount mit den angegeben Parametern, der Status ist erste einmal "unverifiziert" und liefert einen cryptkey, falls das Erstellen erfolgreich war, false falls nicht. Zudem werden alle nötigen Abhängigkeiten erstellt, wie die nötigen Einträge in "Privacy","PersData" und "Usertexts".
*
*@param String Benutzername
*@param String Vorname
*@param String Nachname
*@param String Emailadresse
*@param String Passwort
*
*@return String Verschlüsselungskey oder "false" wenn etwas bei der Erstellung schief geht.
*/
function db_createBenutzerAccount($benutzername, $vorname, $nachname, $email, $passwort) {
	$db = db_connect();
	$sql = "INSERT INTO User (username, password, email, regDate, points, status, idUserGroup, idTrust) VALUES(?,?,LOWER(?),?,0,'nichtVerifiziert',1,1)";
	$stmt = $db->prepare($sql);
	$date = date("Y-m-d");
	$pass_md5 = md5($passwort.$date);
	$fulldate = new DateTime();
	mysqli_stmt_bind_param($stmt, "ssss", $benutzername, $pass_md5, $email,$fulldate->format('Y-m-d H:i:s'));
	$stmt->execute();
	$affected_rows = mysqli_stmt_affected_rows($stmt);
	if($affected_rows == 1) {
		//return true;
	} else {
		echo 'beim erstellen des nutzers ist was schief gegangen '.mysqli_error($db);
		//return false;
	}

	$sql = "INSERT INTO Privacy (idPrivacy, privacykey, cryptkey) VALUES ((SELECT MAX(idUser) FROM User),?,?)";
	$stmt = $db->prepare($sql);

	$cryptkey = md5($benutzername.$date); //Der Cryptkey wird erstellt
	$privacykey = "111111111111111";
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

	$sql = "INSERT INTO UserTexts (idUserTexts) VALUES ((SELECT MAX(idUser) FROM User))";
	$stmt = $db->prepare($sql);
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

/**
*Aktiviert einen Benutzeraccount.
*
*Aktiviert einen Benutzeraccount unter Verwendung des "cryptkeys" der übergeben werden muss. Setzt den Status auf "verifiziert". Gibt "true" zurück.
*
*@param String Cryptkey
*
*@return boolean 
*/
function db_activateAccount($cryptkey) {
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

/**
*Gibt einen Benutzernamen zu einem Cryptkey zurück.
*
*Die Funktion returnt einen Benutzernamen der zu einem schon generierten Cryptkey gehört. Wenn ein nicht existenter Cryptkey eingefügt wird, so gibt die Funktion false zurück.
*
*@param String Cryptkey
*
*@return String Der Benutzername oder einen boolean mit dem Wert "false"
*/
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
}

/**
*Fügt einen neuen Datensatz in "Postalcode" ein.
*
*Funktion ist dazu da, falls eine Postleitzahl neu dazukommt, er in die Datenbank eingetragen wird, ohn zu wissen, welcher Ort sich dahinter verbirgt.
*
*@deprecated 1.0 Eine Hilfsfunktion dessen Hilfe wir nicht mehr benötigen, wird in Zukunft entfernt wenn sicher gestellt ist, das es nirgendswo verwendet wird.
*
*@param Int ID eines Datensatz aus Postalcode
*@param Int Postleitzahl
*@param String Stadtteil
*
*/
function db_fix_plz($idPostal,$plz,$place) {
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

/**
*Gibt die ID zu einer korrekten Kombination aus PLZ und Ort zurück.
*
*Die Funktion soll die zugehörige ID zu einer korrekten Kombination aus PLZ und Ort der Postalcode Tabelle zurück geben. Vorraussetzung ist natürlich,dass Die Kombination schon in der Tabelle ist. Bei einer nicht vorhanden Kombination gibt die Funktion "false" zurück.
*
*@param Int Postleitzahl
*@param String Stadtteil
*
*@return Int Id von dem entsprechendem Datensatz oder "false"
*/
function db_getIdPostalbyPostalcodePlace($plz,$place){
	$db = db_connect();
	$sql = "
		SELECT idPostal 
		FROM Postalcode 
		WHERE postalcode = ?
		AND place = ?";
	$stmt = $db->prepare($sql); 
	$stmt->bind_param('is',$plz,$place);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);
	if(isset($dbentry['idPostal'])){
		return $dbentry['idPostal'];
	}
	else {
		return false;
	}
}

/**
*Gibt die PLZ und Ort zu einer korrekten ID aus Postalcode zurück.
*
*Die Funktion soll die PLZ und den Ort zu einer korrekten ID der Postalcode Tabelle zurück geben. Vorraussetzung ist natürlich,dass Die ID schon in der Tabelle ist. Bei einer nicht vorhanden ID gibt die Funktion "false" zurück.
*
*@param Int ID eines Datenssatzes aus Postalcode
*
*@return mixed[] Array aus zwei Attributen(postalcode,place)
*/
function db_getPostalcodePlacebyIdPostal($idPostal){
	$db = db_connect();
	$sql = "
			SELECT postalcode, place 
			FROM Postalcode
			WHERE idPostal = ?
		";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$thisuser['idPostal']);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);
	return $dbentry;
}

/**
*Gibt eine Auswahl an Daten zu einem Benutzernamen zurück
*
*Die Funktion ist dazu da um einen Nutzer aus der Datenbank mit ausgwählten Daten zu laden. Die Daten werden in einem Array als Werte zurückgegeben. Man muss den Benutzernamen dazu übergeben. Falls es Werte nicht gibt in der Datenbank, werden sie als Leerstrings gesetzt. Die folgenden Werte werden ausgegeben: idUser, password, username, email, regDate, points, trustleveldescription, groupDescription, privacykey, avatar, hobbys, description, firstname, lastname, gender, street, housenumber, idPostal, telefonnumber, messengernumber, birthday
*
*@param String Benutzername
*
*@return mixed[] Array aus verschiedenen Daten mit den Datentypen von Strings und Ints
*/
function db_get_user($user) {
	$db = db_connect();
	$sql = "
		SELECT idUser, password, username, email, regDate, points, trustleveldescription, groupDescription, privacykey, avatar, hobbys, description, firstname, lastname, gender, street, housenumber, idPostal, telefonnumber, messengernumber, birthday, place, postalcode 
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
		    	ON PersData.idPostal = Postalcode.idPostal
		WHERE username = ?";
	$stmt = $db->prepare($sql); 
	$stmt->bind_param('s',$user);
	$stmt->execute();
	$result = $stmt->get_result();
	$thisuser = $result->fetch_assoc();
	if (!isset($thisuser['idPostal'])) {
		$thisuser['postalcode'] = '';
		$thisuser['place'] = '';
		$thisuser['idPostal'] = '';
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

/**
*Speichert die die übergeben Daten für einen Nutzer ab.
*
*Die Funktion ist dazu da um das Profil eines Nutzers upzudaten. Dazu wird der Funktion ein Array mit den Daten übergeben. Je nachdem wo sie hingehören werden sie korrekt abgespeichert. Die folgenden Werte könne upgedatet werden: username, email, regDate, firstname, lastname, birthday, street, housenumber, telefonnumber, messengernumber, idPostal, avatar, hobbys, description, privacykey
*
*@param mixed[] Array aus verschiedenen Daten mit den Datentypen von Strings und Ints
*/
function db_update_user($savedata){
	$savedata['idPostal'] = db_getIdPostalbyPostalcodePlace($savedata['postalcode'],$savedata['place']);
	if($savedata['idPostal']==''){
		$savedata['idPostal'] =-1;
	}
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
		PersData.idPostal = ?,
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
	$stmt->bind_param('ssssssssssissssi',
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
		$savedata['idPostal'],
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

/**
*Löscht alle informationen zu einem User.
*
*Die Funktion löscht alle Informationen zu einem Benutzer. Da dies ein endgültiger Vorgang ist, wird für die Endgültige Löschung die Eingabe des Passworts verlangt. Es werden auch alle Abhängigkeiten entfernt.
*
*@param String Benutzername
*@param String Passwort
*/
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

/**
*Holt sich eine Gute Tat und zusätzliche Parameter.
*
*Durch Übergabe einer Id werden ausgewählte Daten zu einer Tat zurückgegeben. Die Rückgabe erfolgt in Form eines Arrays, in dem die Daten abgespeichert sind. Die folgenden Werte werden abgefragt: Deeds.name, User.username, UserTexts.avatar,Deeds.category, Deeds.street, Deeds.housenumber, Deeds.idPostal,Deeds.starttime, Deeds.endtime, Deeds.organization, Deeds.countHelper, Deeds.status,Trust.idTrust, Trust.trustleveldescription, DeedTexts.description, DeedTexts.pictures, Postalcode.postalcode, Postalcode.place
*
*@param Int Id einer Guten Tat
*
*@return mixed[] Array von den Attributen in Form der Datentypen String und Int
*/
function db_getGuteTat($idGuteTat){
	$db = db_connect();
	$sql = 'SELECT 
		Deeds.name, 
		User.username, 
		UserTexts.avatar,
		Deeds.category, 
		Deeds.street, 
		Deeds.housenumber, 
		Deeds.idPostal,
		Deeds.starttime, 
        Deeds.endtime, 
		Deeds.organization, 
		Deeds.countHelper, 
		Deeds.status,
		Trust.idTrust, 
		Trust.trustleveldescription,
		DeedTexts.description,
		DeedTexts.pictures,
		Postalcode.postalcode,
		Postalcode.place
	FROM Deeds 
		Join User
			On (Deeds.contactPerson = User.idUser)
		Join UserTexts
			On (User.idUser = UserTexts.idUserTexts)
		Join Trust
			On (Deeds.idTrust =	Trust.idTrust)
		Join DeedTexts
			On (Deeds.idGuteTat = DeedTexts.idDeedTexts)
		Join Postalcode
			On (Deeds.idPostal = Postalcode.idPostal)			
	WHERE idGuteTat = ?';
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$idGuteTat);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);
	return $dbentry;
}

// Liefert True wenn der Name schon existiert, sonst false
/**
*Überprüft ob Der Name bei einer Guten Tat schon vergeben ist.
*
*Überprüfung unter Verwendung des Parameters des Namen, ob es schon eine Gute Tat mit dem Namen gibt. Wenn es den Namen gibt, wird "true" zurück gegeben und wenn es ihn noch nicht gibt, so wird "false" zurück gegeben.
*
*@param String Name einer möglichen Guten Tat
*
*@return boolean True oder false
*/
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

/**
*Erstellt eine gute Tat.
*
*Erstellung einer guten Tat. Zudem werden alle nötigen Abhängigkeiten erstellt. Es werden die folgenden Attribute der Funktion übergeben:$name,$user_id,$category,$street,$housenumber,$pid,$starttime,$endtime,$organization,$countHelper,$idTrust,$description,$pictures
*
*@param String Name der guten Tat
*@param Int Id des Erstellers(Benutzer)
*@param String Kategorie
*@param String Straße
*@param Int Hausnummer
*@param Int Id der PLZ/Ort Kombination
*@param String Startzeit
*@param String Endzeit
*@param String Organisation
*@param Int Anzahl der Helfer
*@param Int Vertrauenslevel
*@param Beschreibung der guten Tat
*@param Bilder zu einer guten Tat
*/
function db_createGuteTat($name,$user_id,$category,$street,$housenumber,$pid,$starttime,$endtime,$organization,$countHelper,$idTrust,$description,$pictures){
	$db = db_connect();
	//Datensatz in Deeds einfügen
	//$plz = db_getIdPostalbyPostalcodePlace($postalcode,$place);
	$sql='INSERT INTO Deeds (name, contactPerson, category,street,housenumber,idPostal,starttime,endtime,organization,countHelper,idTrust) VALUES (?,?,?,?,?,?,?,?,?,?,?)';
	$stmt = $db->prepare($sql);
	$stmt->bind_param('sisssisssii', $name, $user_id, $category, $street, 
		$housenumber, $pid, $starttime,$endtime, $organization, $countHelper,
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

/**
*Listet Gute Taten mit einer Auswahlmöglichkeit auf.
*
*Auflistung von guten Taten. Bei der Auflistung kann mit angegeben werden, aber welcher ID und wie viele Gute Taten aufgelistet werden sollen. Zudem kann über einen Filter angegeben werden ob freigegebene, geschlossene oder nur beides angezeigt werden soll. Es werden folgendene Attribute ausgegeben: Deeds.idGuteTat, Deeds.name, Deeds.category, Deeds.street, Deeds.housenumber, Deeds.idPostal, Deeds.organization, Deeds.countHelper, Deeds.status, Trust.idTrust, Trust.trustleveldescription, DeedTexts.description, Postalcode.postalcode, Postalcode.place
*
*@param Int Ab der ID werden die guten Taten aufgelistet
*@param Int Anzahl der aufzulistenden guten Taten
*@param String Filter: 'freigegeben','geschlossen','alle'
*
*@return mixed[] Array aus den ausgewählten Attributen mit den Datentypen String ung Int
*/
function db_getGuteTatenForList($startrow,$numberofrows,$stat){
	$db = db_connect();
	if ($stat == 'alle'){
		$sql = "SELECT 
			Deeds.idGuteTat,
			Deeds.name, 
			Deeds.category, 
			Deeds.street, 
			Deeds.housenumber, 
			Deeds.idPostal,
			Deeds.organization, 
			Deeds.countHelper,
			Deeds.status, 
			Trust.idTrust, 
			Trust.trustleveldescription,
			DeedTexts.description,
			Postalcode.postalcode,
			Postalcode.place
		FROM Deeds 
			Join DeedTexts
				On (Deeds.idGuteTat = DeedTexts.idDeedTexts)
			Join Postalcode
				On (Deeds.idPostal = Postalcode.idPostal)
			Join Trust
				On (Deeds.idTrust =	Trust.idTrust)
		WHERE NOT Deeds.status = 'nichtFreigegeben'
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
	else{
		$sql = "SELECT 
			Deeds.idGuteTat,
			Deeds.name, 
			Deeds.category, 
			Deeds.street, 
			Deeds.housenumber, 
			Deeds.idPostal,
			Deeds.organization, 
			Deeds.countHelper,
			Deeds.status, 
			Trust.idTrust, 
			Trust.trustleveldescription,
			DeedTexts.description,
			Postalcode.postalcode,
			Postalcode.place
		FROM Deeds 
			Join DeedTexts
				On (Deeds.idGuteTat = DeedTexts.idDeedTexts)
			Join Postalcode
				On (Deeds.idPostal = Postalcode.idPostal)
			Join Trust
				On (Deeds.idTrust =	Trust.idTrust)
		WHERE Deeds.status = ?
		LIMIT ? , ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('sii',$stat,$startrow,$numberofrows);
		$stmt->execute();
		$result = $stmt->get_result();
		db_close($db);
		$arr = array();
		while($dbentry =$result->fetch_object()){
			$arr[]= $dbentry;
		}
		return $arr;
	}
}

/**
*Gibt die Gesamtanzahl der guten Taten zurück.
*
*Rückgabe eines Integers welches angibt, wie viele guten Taten insgesamt vorhanden sind. Dabei wird kein Unterschied gemacht, welchen Status sie inne haben.
*
*@return Int Anzahl der guten Taten
*/
function db_getGuteTatenAnzahl(){
	$db = db_connect();
	$sql = "SELECT COUNT(*) FROM Deeds";
	$result = $db->query($sql);
	$dbentry = $result->fetch_assoc();
	db_close($db);
	return $dbentry['COUNT(*)'];
}

/**
*Gibt das Registrierungsdatum des Benutzers zurück.
*
*Die Funktion liest das Registrierungsdatum aus und gibt nur Das Datum und nicth die Uhrzeit zurück. Also wenn dei Abfrage korrekt läuft gibt es das Datum zurück und wenn nicht gibt die Funktion "false" zurück.
*
*@param Int ID des Benutzers
*
*@return String Das Datum oder boolean "false"
*/
function db_regDateOfUserID($userID) {
	$db = db_connect();
	$sql = "SELECT regDate FROM User WHERE idUser = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$userID);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	if(isset($dbentry['regDate'])){
		//echo 'RegDate '.$dbentry['regDate'];
		$dateTeile = explode(" ", $dbentry['regDate']); //Im Datestring ist auch die Zeit, wir wollen nur das Datum (siehe Erstellung des Benutzeraccounts)
		db_close($db);
		return $dateTeile[0];
	}
	else {
		echo "Error: ".mysqli_error($db);
		db_close($db);
		return false;
	}
}

/**
*Liefert den PasswortHash zu einer UserID oder false.
*
*Die Funktion bekommt als Parameter die Benutzer ID übergeben und ließt zu der ID den zugehörigen Passwort Hash aus Datenbank. Falls es die Benutzer ID nicht gibt, so wird false zurück gegeben.
*
*@param Int ID des Benutzers
*
*@return String Das PasswortHash oder boolean "false"
*/
function db_passwordHashOfUserID($userID) {
	$db = db_connect();
	$sql = "SELECT password FROM User WHERE idUser = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$userID);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	if(isset($dbentry['password'])){
		db_close($db);
		return $dbentry['password'];
	}
	else {
		echo "Error: ".mysqli_error($db);
		db_close($db);
		return false;
	}
}

/**
*Liefert den Status zu einer UserID oder false.
*
*Die Funktion bekommt als Parameter die Benutzer ID übergeben und ließt zu der ID den zugehörigen Status aus Datenbank. Falls es die Benutzer ID nicht gibt, so wird false zurück gegeben.
*
*@param Int ID des Benutzers
*
*@return String Den Status oder boolean "false"
*/
function db_statusByUserID($userID) {
	$db = db_connect();
	$sql = "SELECT status FROM User WHERE idUser = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$userID);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	if(isset($dbentry['status'])){
		db_close($db);
		return $dbentry['status'];
	}
	else {
		echo "Error: ".mysqli_error($db);
		db_close($db);
		return false;
	}
}

/**
*Überprüft ob eine Gute Tat mit einer bestimmten ID gibt.
*
*Die Funktion bekommt als Parameter eine Gute Tat ID übergeben und überprüft ob die ID schon für ein Gute Tat vergeben wurde. Falls es die Gute Tat ID nicht gibt, so wird false zurück gegeben.
*
*@param Int ID einer Guten Tat
*
*@return boolean "true" oder "false"
*/
function db_doesGuteTatExists($idGuteTat) {
	$db = db_connect();
	$sql = "SELECT name FROM Deeds WHERE idGuteTat = ? ";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$idGuteTat);
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

/**
*Überprüft ob ein Benutzer sich schon für eine bestimme Gute Tat beworben hat.
*
*Die Funktion bekommt als Parameter eine Gute Tat ID und eine Benutzer ID übergeben. Sie prüft ob der Benutzer sich schon für eine bestimmte Gute Tat beworben hat. Ist das der Fall, so gibt die Funktion true zurück und sonst false.
*
*@param Int ID einer Guten Tat
*@param Int ID eines Benutzers
*
*@return boolean "true" oder "false"
*/
function db_isUserCandidateOfGuteTat($idGuteTat, $idUser) {
	$db = db_connect();
	$sql = "SELECT idUser FROM Application WHERE idUser = ? AND idGuteTat = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('ii',$idUser, $idGuteTat);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);
	if(isset($dbentry['idUser'])){
		return true;
	}
	else {
		return false;
	}
}

/**
*Gibt die ID der Kontaktperson zu einer Guten Tat zurück.
*
*Die Funktion bekommt als Parameter eine Gute Tat ID übergeben und gibt die ID der dazugehörigen Kontaktperson zurück. Falls dies fehlschlägt aufgrund verschiedener möglicher Einflüsse, so gibt die Funktion false zurück.
*
*@param Int ID einer Guten Tat
*
*@return Int ID der Kontaktperson oder "false"
*/
function db_getUserIdOfContactPersonByGuteTatID($idGuteTat) {
	$db = db_connect();
	$sql = "SELECT idUser FROM User U JOIN Deeds D on (U.idUser = D.contactPerson) WHERE idGuteTat = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$idGuteTat);
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

/**
*Gibt den Status einer Guten Tat zurück.
*
*Die Funktion bekommt als Parameter eine Gute Tat ID übergeben und gibt den Status der Guten Tat zurück. Wenn dies fehlschlägt, so gitb die Funktion "false" zurück.
*
*@param Int ID einer Guten Tat
*
*@return String Status oder "false"
*/
function db_getStatusOfGuteTatById($idGuteTat) {
	$db = db_connect();
	$sql = "SELECT status FROM Deeds WHERE idGuteTat = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$idGuteTat);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);
	if(isset($dbentry['status'])) {
		return $dbentry['status'];
	}
	else {
		return false;
	}
}

/**
*Überprüft ob die angenommen Bewerberber die selbe Ańzahl wie der geforderten Anzahl Helfer ist.
*
*Die Funktion bekommt als Parameter eine Gute Tat ID übergeben und  überprüft ob die maximale Anzahl der geforderten Helfer erreicht ist oder nicht. Wenn die maximale Anzahl erreicht ist, gibt die Funktion true zurück. Wenn nicht so wird false zurück gegeben.
*
*@param Int ID einer Guten Tat
*
*@return boolean "true" oder "false"
*/
function db_isNumberOfAcceptedCandidatsEqualToRequestedHelpers($idGuteTat) {
	$db = db_connect();
	$sql = "SELECT Count(idUser) As helperCount FROM HelperForDeed WHERE idGuteTat = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$idGuteTat);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();

	if(isset($dbentry['helperCount'])) {
		$helperCount =  $dbentry['helperCount'];
		$sql = "SELECT countHelper As requestedHelperCount FROM Deeds WHERE idGuteTat = ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('i',$idGuteTat);
		$stmt->execute();
		$result = $stmt->get_result();
		$dbentry = $result->fetch_assoc();
		db_close($db);
		if(isset($dbentry['requestedHelperCount'])) {
			return $helperCount === $dbentry['requestedHelperCount'];
		}
		else {
				return false;
		}
	}
	else {
		db_close($db);
		return false;
	}
}

/**
*Gibt den Status einer Bewerbung zurück.
*
*Die Funktion bekommt als Parameter eine Gute Tat ID und die ID des Bewerbers übergeben. Sie gibt den Status der Bewerbung von dem Bewerber bei der bestimmten guten Tat zurück
*
*@param Int ID einer Guten Tat
*@param Int ID des Benutzers/Bewerbers
*
*@return String Status der Bewerbung oder "false"
*/
function db_getStatusOfBewerbung($idUser, $idGuteTat) {
	$db = db_connect();
	$sql = "SELECT status FROM Application WHERE idUser = ? AND idGuteTat = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('ii',$idUser, $idGuteTat);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);
	if(isset($dbentry['status'])) {
		return $dbentry['status'];
	}
	else {
		return false;
	}
}

/**
*Gibt die Email eines Benutzers zurück.
*
*Die Funktion bekommt als Parameter eine Benutzer ID übergeben und gibt die Emailadresse dazu zurück. Falls dies fehlschlägt aufgrund verschiedener möglicher Einflüsse, so gibt die Funktion false zurück.
*
*@param Int ID eines Benutzers
*
*@return String Emailadresse oder "false"
*/ 
function db_getMailOfBenutzerByID($idUser) {
	$db = db_connect();
	$sql = "SELECT email FROM User WHERE idUser = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$idUser);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);
	if(isset($dbentry['email'])){
		return $dbentry['email'];
	}
	else {
		return false;
	}
}

/**
*Gibt den Namen einer Guten Tat zurück.
*
*Die Funktion bekommt als Parameter eine Gute Tat ID übergeben und gibt den Namen dazu zurück. Falls dies fehlschlägt aufgrund verschiedener möglicher Einflüsse, so gibt die Funktion false zurück.
*
*@param Int ID einer Guten Tat
*
*@return String Name oder "false"
*/ 
function db_getNameOfGuteTatByID($idGuteTat) {
	$db = db_connect();
	$sql = "SELECT name FROM Deeds WHERE idGuteTat = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$idGuteTat);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);
	if(isset($dbentry['name'])){
		return $dbentry['name'];
	}
	else {
		return false;
	}
}

/**
*Gibt den Namen der Kontaktperson einer Guten Tat zurück.
*
*Die Funktion bekommt als Parameter eine Gute Tat ID übergeben und gibt den Namen der Kontaktperson von der guten Tat dazu zurück. Falls dies fehlschlägt aufgrund verschiedener möglicher Einflüsse, so gibt die Funktion false zurück.
*
*@param Int ID einer Guten Tat
*
*@return String Name oder "false"
*/ 
function db_getUsernameOfContactPersonByGuteTatID($idGuteTat) {
	$db = db_connect();
	$sql = "SELECT username FROM User U JOIN Deeds D on (U.idUser = D.contactPerson) WHERE idGuteTat = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$idGuteTat);
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

}

/**
*Gibt die Emailadresse der Kontaktperson einer Guten Tat zurück.
*
*Die Funktion bekommt als Parameter eine Gute Tat ID übergeben und gibt die Emailadresse der Kontaktperson dazu zurück. Falls dies fehlschlägt aufgrund verschiedener möglicher Einflüsse, so gibt die Funktion false zurück.
*
*@param Int ID einer Guten Tat
*
*@return String Emailadresse oder "false"
*/ 
function db_getEmailOfContactPersonByGuteTatID($idGuteTat) {
	$db = db_connect();
	$sql = "SELECT email FROM User U JOIN Deeds D on (U.idUser = D.contactPerson) WHERE idGuteTat = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$idGuteTat);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbentry = $result->fetch_assoc();
	db_close($db);
	if(isset($dbentry['email'])){
		return $dbentry['email'];
	}
	else {
		return false;
	}
}

/*Liefert den Usernamen zu einem Benutzeraccount mit der idUser = $idUser oder false*/
/**
*Gibt den Namen eines Benutzers zurück.
*
*Die Funktion bekommt als Parameter eine Benutzer ID übergeben und gibt den Namen des Benutzers dazu zurück. Falls dies fehlschlägt aufgrund verschiedener möglicher Einflüsse, so gibt die Funktion false zurück.
*
*@param Int ID eines Benutzers
*
*@return String Benutzername oder "false"
*/ 
function db_getUsernameOfBenutzerByID($idUser) {
	$db = db_connect();
	$sql = "SELECT username FROM User WHERE idUser = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i',$idUser);
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
}

/**
*Fügt eine Bewerbung in die Datenbank ein.
*
*Die Funktion bekommt als Parameter eine Gute Tat ID, eine Benutzer ID und den Bewerbungstext übergeben und legt mit den Daten eine Bewerbung an. Ist eine Anlegung einer Bewerbung erfolgreich so gibt die Funktion true zurück. Falls dies fehlschlägt aufgrund verschiedener möglicher Einflüsse, so gibt die Funktion false zurück. Zudem werden noch folgende Werte gesetzt: $status = 'offen', $replyText = NULL
*
*@param Int ID eines Benutzers
*@param Int ID einer Guten Tat
*@param String Bewerbungstext
*
*@return boolean "true" oder "false"
*/ 
function db_addBewerbung($idUser, $idGuteTat, $Bewerbungstext) {
	$db = db_connect();
	$sql = "INSERT INTO Application (idUser, idGuteTat, applicationText, status) VALUES (?,?,?,'offen')";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('iis',$idUser, $idGuteTat, $Bewerbungstext);
	$stmt->execute();
	$affected_rows = mysqli_stmt_affected_rows($stmt);
	db_close($db);
	if($affected_rows == 1) {
		return true;
	} else {
		echo 'Beim Erstellen der Bewerbung in der Datenbank ist etwas schief belaufen '.mysqli_error($db);
		return false;
	}
}

/**
*Setzt den Status einer Bewerbung in "angenommen" um.
*
*Die Funktion bekommt als Parameter eine Benutzer ID, eine Gute Tat ID und die Antwort der Kontaktperson übergeben und setzt den Status in "angenommen" um. Zudem wird ein Eintrag in einer neuer Tabelle angelegt, in denen die akzeptierten Bewerbungen gespeichert werden. Ist eine Akzeptierung einer Bewerbung erfolgreich so gibt die Funktion true zurück. Falls dies fehlschlägt aufgrund verschiedener möglicher Einflüsse, so gibt die Funktion false zurück. 
*
*@param Int ID eines Benutzers
*@param Int ID einer Guten Tat
*@param String Erklärung der Kontaktperson
*
*@return boolean "true" oder "false"
*/ 	
function db_acceptBewerbung($candidateID, $idGuteTat, $explanation) {
	$db = db_connect();
	$sql = 'UPDATE Application SET `status` = "angenommen", `replyMsg` = ? WHERE idUser = ? AND idGuteTat = ?';
	//echo $sql;
	//echo $explanation."  ".$idGuteTat."  ".$candidateID;
	$stmt = $db->prepare($sql);
	$stmt->bind_param('sii',$explanation, $candidateID, $idGuteTat);
	$stmt->execute();
	$affected_rows = mysqli_stmt_affected_rows($stmt);

	if($affected_rows == 1) {
		//Eintrag in HelpferForDeed einfügen
		$sql = "INSERT INTO HelperForDeed (idUser, idGuteTat, rating) VALUES (?,?,0)";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('ii',$candidateID, $idGuteTat);
		$stmt->execute();
		$affected_rows = mysqli_stmt_affected_rows($stmt);
		db_close($db);
		if($affected_rows == 1) {
			return true;
		} else {
			echo 'Beim Hinzufügen des Benutzers in der Datenbank zu den Helfern der guten Tat ist etwas schief gegangen '.mysqli_error($db);
			return false;
		}
		return true;
	} else {
		echo 'Beim Aktualisieren der Bewerbungsinformation in der Datenbank ist etwas schief gegangen '.mysqli_error($db);
		db_close($db);
		return false;
	}
}

/**
*Setzt den Status einer Bewerbung in "abgelehnt" um.
*
*Die Funktion bekommt als Parameter eine Benutzer ID, eine Gute Tat ID und die Antwort der Kontaktperson übergeben und setzt den Status in "abgelehnt" um. Ist eine Ablehnung einer Bewerbung erfolgreich so gibt die Funktion true zurück. Falls dies fehlschlägt aufgrund verschiedener möglicher Einflüsse, so gibt die Funktion false zurück. 
*
*@param Int ID eines Benutzers
*@param Int ID einer Guten Tat
*@param String Erklärung der Kontaktperson
*
*@return boolean "true" oder "false"
*/ 	
function db_declineBewerbung($candidateID, $idGuteTat, $explanation) {
	$db = db_connect();
	$sql = "UPDATE Application SET status = 'abgelehnt', replyMsg = ? WHERE idUser = ? AND idGuteTat = ?";
	$stmt = $db->prepare($sql);
	$stmt->bind_param('sii',$explanation, $candidateID, $idGuteTat);
	$stmt->execute();
	$affected_rows = mysqli_stmt_affected_rows($stmt);
	db_close($db);
	if($affected_rows == 1) {
		return true;
	} else {
		echo 'Beim Aktualisieren der Bewerbungsinformation in der Datenbank ist etwas schief gegangen '.mysqli_error($db);
		return false;
	}

}

?>
