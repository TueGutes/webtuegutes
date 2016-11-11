<?php
//@author: Andreas Blech
/*Description: Auf dieser Seite werden zwei Szenarien behandelt.
* 1. Ein Nutzer bewirbt sich für eine gute Tat eines anderen Nutzers
  2. Ein Nutzer schaut sich die Bewerbung eines anderen Nutzers für seine gute Tat an und kann diese annehmen oder ablehnen
*/

require "./includes/DEF.php";
include './includes/ACCESS.php';
require "./includes/_top.php";
//Inkludieren von script-Dateien
include './includes/db_connector.php';

//TODO: DB Funktionen, die später ausgelagert werden sollten

/*Liefert true, wenn eine Gute Tat mit der idGuteTat = $idGuteTat existiert, sonst false*/
function doesGuteTatExists($idGuteTat) {
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

/*Liefert true, wenn sich der Bewerber mit der idUser = $idUser
bereits für die Gute Tat mit der idGuteTat = $idGuteTat beworben hat.
false, falls nicht*/
function isUserCandidateOfGuteTat($idGuteTat, $idUser) {
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

/*Liefert die userID der Kontaktperson zu der Guten Tat mit der idGuteTat = $idGuteTat oder false*/
function getUserIdOfContactPersonByGuteTatID($idGuteTat) {
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

/*Liefert den Status der Guten Tat mit der idGuteTat = $idGuteTat oder false*/
function getStatusOfGuteTatById($idGuteTat) {
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

/*Liefert true, wenn die Anzahl der Helfer(Anzahl angenommener Bewerbungen)
 gleich der Anzahl der angeforderten Helfer für die Gute Tat mit der idGuteTat = $idGuteTat
 ansonsten false*/
function isNumberOfAcceptedCandidatsEqualToRequestedHelpers($idGuteTat) {
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

/*Gibt den Status der Bewerbung des Bewerbers mit der idUser = $idUser
zu der Guten Tat mit der idGuteTat = $idGuteTat zurück, oder false*/
function getStatusOfBewerbung($idUser, $idGuteTat) {
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

/*Gibt die Email Adresse des Benutzers mit der idUser = $idUser
 zurück oder false*/
function getMailOfBenutzerByID($idUser) {
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

/*Gibt den Namen der Guten Tat mit der idGuteTat = $idGuteTat zurück oder false, falls keine gute Tat existiert*/
function getNameOfGuteTatByID($idGuteTat) {
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

/*Liefert den Usernamen der ContactPerson der Guten Tat mit der idGuteTat = $idGuteTat oder false*/
function getUsernameOfContactPersonByGuteTatID($idGuteTat) {
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

/*Liefert die Email-Adresse der ContactPerson der Guten Tat mit der idGuteTat = $idGuteTat oder false*/
function getEmailOfContactPersonByGuteTatID($idGuteTat) {
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
function getUsernameOfBenutzerByID($idUser) {
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

/*Fügt einen neuen Eintrag in der Relation Application hinzu
	idUser = $idUser
	idGuteTat = $idGuteTat
	$applicationText = $Bewerbungstext
	$status = 'offen'
	$replyText = NULL
	liefert true im Erfolgsfall, ansonsten false
*/
function addBewerbung($idUser, $idGuteTat, $Bewerbungstext) {
	$db = db_connect();
	$sql = "Insert into Application (idUser, idGuteTat, applicationText, status) values (?,?,?,'offen')";
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

/*Setzt den status der Bewerbung die zu idUser=$candidateID und idGuteTat=$idGuteTat gehört auf 'angenommen'
	und setzt die replyMsg auf $Begruendungstext
	Erzeugt zusätzlich einen neuen Eintrag in der Relation HelpferForDeed mit den gleichen Daten und dem rating 0
*/
function acceptBewerbung($candidateID, $idGuteTat, $explanation) {
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
		$sql = "Insert into HelperForDeed (idUser, idGuteTat, rating) values (?,?,0)";
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

/*Setzt den status der Bewerbung die zu idUser=$candidateID und idGuteTat=$idGuteTat gehört auf 'abgelehnt'
	und die replyMsg auf $explanation
	Liefert true im Erfolgsfall, sonst false
*/
function declineBewerbung($candidateID, $idGuteTat, $explanation) {
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


/*
Es gibt ... Fälle:
	0. Der Nutzer ist nicht eingeloggt und darf sich keine Bewerbungen anschauen
	1. Ein Nutzer bewirbt sich für eine gute Tat (isset $_GET['tatID'])
		1.1 Es existiert keine gute Tat zu dieser ID (doesGuteTatExists())
		1.2 Der Nutzer hatte sich bereits für diese Tat beworben (isUserCandidateOfGuteTat()
		1.3 Der Nutzer selbst hat die Tat erstellt (contactPersonOfGuteTatById())
		1.4 Die gute Tat wurde bereits abgeschlossen (getStatusOfGuteTatById())
		1.5 Die Anzahl der angenommenen Bewerbungen ist gleich der Anzahl der benötigten Helfer (isNumberOfAcceptedCandidatsEqualToRequestedHelpers()) : Hinweis: Man ist nur Reserve, aber bewerben kann man sich trotzdem
		1.6 alles in Ordnung, der Nutzer kann sich mit einem Bewerbungstext bewerben
			  und seine Bewerbung abschicken.
	2. Ein Nutzer schaut sich die Bewerbung eines anderen Benutzers an isset $_GET['tatID'] && $_GET['userID']
		1.1 Der Benutzer hat die Tat nicht erstellt und darf keine Bewerbungen annehmen (contactPersonOfGuteTatById())
		1.2 Die Bewerbung wurde bereits akzeptiert (getStatusOfBewerbung())
		1.3 Die Bewerbung wurde bereits abgelehnt (getStatusOfBewerbung())
		1.4 Es gibt keine Bewerbung von diesem Nutzer zu dieser Tat
		1.5 Anzahl der Helfer zu groß, um sie anzunehmen
		1.6 alles in Ordnung, der Nutzer kann die Bewerbung mit einem Text annehmen oder ablehnen
	3. Fall: Bewerbungsformular wurde abgeschickt.  Es wird eine Email an den Ersteller der Guten Tat gesendet mit dem Link zur Bewerbung (addBewerbung()). Es wird eine Bestätigung angezeigt und ein Link zur Detailseite der guten Tat. Außerdem wird ein Eintrag in der Datenbank vorgenommen
	4. Fall: Bewerbung-Annahme Formular wurde abgeschickt. Der Bewerber bekommt eine Anname-Email mit dem Link zur Detailseite der guten Tat 	(acceptBewerbung()) der Datenbankeintrag wird angepasst, ein neuer Eintrag in der Datenbank für die Helfer vorgenommen und eine Bestätigung + Plus Link zur Detailseite der Guten Tat wird angezeigt
	5. Fall: Bewerbung-Absage Formular wurde abgeschickt. Der Bewerber bekommt eine Absage-Email mit dem Link zur Detailseite der guten Tat
	(declineBewerbung()) der Datenbankeintrag wird angepasst und eine Bestätigung + LInk zur Detailseite der Guten Tat wird angezeigt
*/
//Fall 0: Profile sind nur für eingeloggte Nutzer sichtbar:
//if (!$_USER->loggedIn()) die ('<h3>Bewerbungen sind nur für eingeloggte Nutzer sichtbar!</h3><p/><a href="./login">Zum Login</a>');

echo '<h2>Bewerbung</h2>';

//TODO: Das hier muss im Link der Email gesetzt sein
//Fall 1: Bewerbung abschicken
if(isset($_GET['idGuteTat']) && !isset($_GET['candidateID'])) {
	$idGuteTat = $_GET['idGuteTat'];
	$idUser = $_USER->getID();

	if(!doesGuteTatExists($idGuteTat)) {
		//Fall 1.1: Es existiert keine gute Tat mit der übergebenen ID
		echo '<h3><red>Die gute Tat konnte nicht gefunden werden :(</red></h3>';
	}
	else if(isUserCandidateOfGuteTat($idGuteTat, $idUser)) {
		//Fall 1.2: Der Nutzer hatte sich bereits für diese Tat beworben
		echo '<h3><red>Du hast dich bereits für diese Gute Tat beworben</red></h3>';
	}
	else if(getUserIdOfContactPersonByGuteTatID($idGuteTat) === $idUser) {
		//Fall 1.3: Der Bewerber selbst hat die gute Tat erstellt
		echo '<h3><red>Du kannst dich nicht für eine gute Tat bewerben, die du selbst ausgeschieben hast</red></h3>';
	}
	else if(getStatusOfGuteTatById($idGuteTat) === 'geschlossen') {
		//Fall 1.4: Die Gute Tat ist bereits geschlossen
		echo '<h3><red>Diese gute Tat wurde bereits abgeschlossen</red></h3>';
	}
	else {
		if(isNumberOfAcceptedCandidatsEqualToRequestedHelpers($idGuteTat) === true) {
			//Fall 1.5: Die Anzahl der angenommenen Bewerbungen erfüllt die Anzahl der gewünschten Helfer
			echo '<h4>Hinweis: Diese gute Tat hat bereits genügend Helfer, du wirst u.U. als Reserve eingesetzt</h4>';
		}
		//Fall 1.6 (und 1.5): Nutzer darf die Bewerbung inkl. eines Bewerbungstextes abschicken

		$_SESSION['idGuteTat'] = $idGuteTat; //Zwischenspeichern, um nach dem Absenden darauf zugreifen zu können

//<table> <tr> <td>
//<input type="textbox" name="Bewerbungstext"><br><br><br>
//<b>Bewerbungstext:</b><br>
		echo '<div class="center">
		<form action="deeds_bewerbung" method="post">
				<textarea id="bewerbungstext" name="Bewerbungstext" cols="80" rows="6" placeholder="Bewerbungstext - Schreibe etwas zu deiner Bewerbung um die gute Tat"></textarea><br><br>
				<input type="submit" value="Bewerbung abschicken">
		</form>
		</div>';

	}

}
//Fall 2: Bewerbung annehmen oder ablehnen
else if(isset($_GET['idGuteTat']) && isset($_GET['candidateID'])) {
	$idGuteTat = $_GET['idGuteTat'];
	$candidateID = $_GET['candidateID'];
	$idUser = $_USER->getID();
	$status = getStatusOfBewerbung($candidateID, $idGuteTat);
	if(getUserIdOfContactPersonByGuteTatID($idGuteTat) != $idUser) {
		//Fall 1.1: Der Nutzer hat die gute Tat nicht erstellt und darf dementsprechend ihre Bewerbungen nicht annehmen
		echo '<h3><red>Du darfst nur Bewerbungen zu guten Taten einsehen, die du selbst erstellt hat</red></h3>';
	}
	else if($status === "angenommen") {
		//Fall 1.2: Die Bewerbung wurde bereits angenommen
		echo '<h3><red>Die Bewerbung wurde bereits angenommen - der Bewerber wurde informiert</red></h3>';
	}
	else if($status === "abgelehnt") {
		//Fall 1.3: Die Bewerbung wurde bereits abgelehnt
		echo '<h3><red>Die Bewerbung wurde bereits abgelehnt - der Bewerber wurde informiert</red></h3>';
	}
	else if($status === false) {
		//Fall 1.4: Es existiert keine Bewerbung dieses Nutzers für die gute Tat (sollte Websitetechnisch niemals passieren)
		echo '<h3><red>Es existiert keine Bewerbung des Bewerbers für die Tat</red></h3>';
	}
	else if(isNumberOfAcceptedCandidatsEqualToRequestedHelpers($idGuteTat) === true) {
		//Fall 1.5: Anzahl der angeforderten Helfer bereits erreicht, Bewerbung kann nicht angenommen werden, bleibt ausstehend
		echo '<h3><red>Die Anzahl der angeforderten Helfer für diese Tat wurde bereits erreicht. </p> Die Bewerbung muss u.U. später akzeptiert werden, falls ein Helfer absagt.</red></h3>';
	}
	else {
		//Fall 1.6: Bewerbung ist okay und kann angenommen oder abgelehnt werden inkl. einer Begründung.

		//Link zum Profil des Bewerbers
		//TODO: Link zum Profil mit richtigem Parameter
		echo '<a href="./profile?user='.$candidateID.'">Zum Benutzer-Profil des Bewerbers</a><br><br>';

		$_SESSION['idGuteTat'] = $idGuteTat; //Zwischenspeichern, um nach dem Absenden darauf zugreifen zu können
		$_SESSION['$candidateID'] = $candidateID;

		echo '<div class="center">
		<form action="deeds_bewerbung" method="post">
				<textarea id="begruendungstext" name="Begruendungstext" cols="80" rows="3" placeholder="Begründung - schreibe dem Bewerber eine Begründung deiner Absage bzw. Annahme seiner Bewerbung"></textarea><br><br>
				<input type="submit" value="Annehmen" name="AnnehmenButton">
				<input type="submit" value="Ablehnen" name="AblehnenButton">
		</form>
		</div>';


	}
}
else if(isset($_POST['Bewerbungstext'])) {
	//Fall 3: Bewerbungsformular wurde abgeschickt
	//TODO: Variablen setzen
	$Bewerbungstext = $_POST['Bewerbungstext'];
	$idUser = $_USER->getID();
	$idGuteTat = $_SESSION['idGuteTat'];
	unset($_SESSION['idGuteTat']); //Zwischengespeicherte Variable lesen und anschließend löschen
	$UsernameOfBewerber = $_USER->getUsername();

	$NameOfGuteTat = getNameOfGuteTatByID($idGuteTat);
	$UsernameOfErsteller = getUsernameOfContactPersonByGuteTatID($idGuteTat);
	$MailOfErsteller = getEmailOfContactPersonByGuteTatID($idGuteTat);

	//URL der Bewerbungsseite generieren
	$actual_link = $HOST."/deeds_bewerbung"."?idGuteTat=$idGuteTat&candidateID=$idUser";
	//$actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?idGuteTat=$idGuteTat&candidateID=$idUser";
	$MailSubject = "Neue Bewerbung für '$NameOfGuteTat'";
	//$MailContent = "$UsernameOfBewerber hat sich für deine gute Tat '$NameOfGuteTat' beworben. Er schreibt dazu: $Bewerbungstext Besuche die URL, um Details zur Bewerbung einzusehen $actual_link";

	$MailContent =
		"<div style=\"margin-left:10%;margin-right:10%;background-color:#757575\">
			<img src=\"img/wLogo.png\" alt=\"TueGutes\" title=\"TueGutes\" style=\"width:25%\"/>
		</div>
		<h2>Hallo $UsernameOfBewerber!</h2><br>
		<h3>$UsernameOfBewerber hat sich für deine gute Tat '$NameOfGuteTat' beworben. <br>
		<h3>Er schreibt dazu: \"$Bewerbungstext\"</h3><br>
		<h3>Besuche die URL, um Details zur Bewerbung einzusehen $actual_link</h3>";



	//Sende mail an Ersteller der guten Tat
	sendEmail($MailOfErsteller, $MailSubject, $MailContent);
	//Datenbank Eintrag
	addBewerbung($idUser, $idGuteTat, $Bewerbungstext);
	//Bestätigung anzeigen
	echo '<h2><green>Deine Bewerbung wurde erfolgreich abgeschickt</green></h2>';
	//TODO: Link zu Detailseite der guten Tat
	echo '<a href="./deeds_details?id='.$idGuteTat.'">Zur \'Guten Tat\'</a>';

}
else if(isset($_POST['AnnehmenButton'])) {
	//Fall 4: Bewerbungs-Annahme Formular wurde abgeschickt
	//TODO: Variablen setzen
	$Begruendungstext = $_POST['Begruendungstext'];
	$idGuteTat = $_SESSION['idGuteTat']; //Zwischengespeicherte Variablen laden und anschließend löschen
	unset($_SESSION['idGuteTat']);
	$candidateID = $_SESSION['$candidateID'];
	unset($_SESSION['candidateID']);
	$UsernameOfErsteller = $_USER->getUsername();

	$MailOfBewerber = getMailOfBenutzerByID($candidateID);
	$UsernameOfBewerber = getUsernameOfBenutzerByID($candidateID);
	$NameOfGuteTat = getNameOfGuteTatByID($idGuteTat);

	$MailSubject = "Bewerbung angenommen!";
	//$MailContent = "Hallo $UsernameOfBewerber! Deine Bewerbung für die gute Tat '$NameOfGuteTat' wurde von $UsernameOfErsteller angenommen! Er schreibt dazu: $Begruendungstext";

	$MailContent =
		"<div style=\"margin-left:10%;margin-right:10%;background-color:#757575\">
			<img src=\"img/wLogo.png\" alt=\"TueGutes\" title=\"TueGutes\" style=\"width:25%\"/>
		</div>
		<h2>Hallo $UsernameOfBewerber!</h2><br>
		<h3>Deine Bewerbung für die gute Tat '$NameOfGuteTat' wurde von $UsernameOfErsteller mit folgender Begründung angenommen!:</h3> <br> \"$Begruendungstext\"";


	//Sende Mail an Bewerber
	sendEmail($MailOfBewerber, $MailSubject, $MailContent);
	//Datenbankeintrag anpassen
	//neuer Datenbankeintrag in der Helper Relation
	acceptBewerbung($candidateID, $idGuteTat, $Begruendungstext);
	//Bestätigung anzeigen
	echo '<h2><green>Der Bewerber wurde über die Annahme seiner Bewerbung informiert</green></h2>';
	//TODO: Link zu Detailseite der guten Tat
	echo '<a href="./deeds_details?id='.$idGuteTat.'">Zur \'Guten Tat\'</a>';
}
else if(isset($_POST['AblehnenButton'])) {
	//Fall 5: Bewerbung-Absage Formular wurde abgeschickt
	//TODO: Variablen setzen
	$Begruendungstext = $_POST['Begruendungstext'];
	$idGuteTat = $_SESSION['idGuteTat']; //Zwischengespeicherte Variablen laden und anschließend löschen
	unset($_SESSION['idGuteTat']);
	$candidateID = $_SESSION['$candidateID'];
	unset($_SESSION['candidateID']);
	$UsernameOfErsteller = $_USER->getUsername();

	$MailOfBewerber = getMailOfBenutzerByID($candidateID);
	$UsernameOfBewerber = getUsernameOfBenutzerByID($candidateID);
	$NameOfGuteTat = getNameOfGuteTatByID($idGuteTat);

	$MailSubject = "Bewerbung abgelehnt!";
	$MailContent =
		"<div style=\"margin-left:10%;margin-right:10%;background-color:#757575\">
			<img src=\"img/wLogo.png\" alt=\"TueGutes\" title=\"TueGutes\" style=\"width:25%\"/>
		</div>
		<h2>Hallo $UsernameOfBewerber!</h2><br>
		<h3>Deine Bewerbung für die gute Tat '$NameOfGuteTat' wurde von $UsernameOfErsteller mit folgender Begründung abgelehnt:</h3> <br> \"$Begruendungstext\"";


	/*$mailcontent =
		"<div style=\"margin-left:10%;margin-right:10%;background-color:#757575\">
			<img src=\"img/wLogo.png\" alt=\"TueGutes\" title=\"TueGutes\" style=\"width:25%\"/>
		</div>
		<div style=\"margin-left:10%;margin-right:10%\">
			<h1>Herzlich Willkommen <b>".$vorname."</b> bei 'Tue Gutes in Hannover':</h1>
			<h3>Klicke auf den Link, um deine Registrierung abzuschließen: ".$actual_link."?c=".$cryptkey." </h3>
		</div>";
*/

	//Sende Absage-Mail an Bewerber
	sendEmail($MailOfBewerber, $MailSubject, $MailContent);
	//Datenbankeintrag anpassen
	declineBewerbung($candidateID, $idGuteTat, $Begruendungstext);
	//Bestätigung anzeigen
	echo '<h2><green>Der Bewerber wurde über die Ablehnung seiner Bewerbung informiert</green></h2>';
	//TODO: Link zu Detailseite der guten Tat
	echo '<a href="./deeds_details?id='.$idGuteTat.'">Zur \'Guten Tat\'</a>';
}
else {
	//Die URL wurde ohne Argumente aufgerufen
	echo '<h3><red>Der Bewerbungslink wurde mit ungültigen Argumenten aufgerufen</red></h3>';
}


require "./includes/_bottom.php";
?>
