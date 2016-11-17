<?php
//@author: Andreas Blech
/*Beschreibung: Auf dieser Seite werden zwei Szenarien behandelt.
* 1. Ein Nutzer bewirbt sich für eine gute Tat eines anderen Nutzers
  2. Ein Nutzer schaut sich die Bewerbung eines anderen Nutzers für seine gute Tat an und kann diese annehmen oder ablehnen
*/

require "./includes/DEF.php";
include './includes/ACCESS.php';
require "./includes/_top.php";
//Inkludieren von script-Dateien
include './includes/db_connector.php';

/*
Es gibt 6 Fälle:
	0. Der Nutzer ist nicht eingeloggt und darf sich keine Bewerbungen anschauen (ACCESS.php)
	1. Ein Nutzer bewirbt sich für eine gute Tat (isset $_GET['tatID'])
		1.1 Es existiert keine gute Tat zu dieser ID (db_doesGuteTatExists())
		1.2 Der Nutzer hatte sich bereits für diese Tat beworben (db_isUserCandidateOfGuteTat()
		1.3 Der Nutzer selbst hat die Tat erstellt (contactPersonOfGuteTatById())
		1.4 Die gute Tat wurde bereits abgeschlossen (db_getStatusOfGuteTatById())
		1.5 Die Anzahl der angenommenen Bewerbungen ist gleich der Anzahl der benötigten Helfer (db_isNumberOfAcceptedCandidatsEqualToRequestedHelpers()) : Hinweis: Man ist nur Reserve, aber bewerben kann man sich trotzdem
		1.6 alles in Ordnung, der Nutzer kann sich mit einem Bewerbungstext bewerben
			  und seine Bewerbung abschicken.
	2. Ein Nutzer schaut sich die Bewerbung eines anderen Benutzers an isset $_GET['tatID'] && $_GET['userID']
		1.1 Der Benutzer hat die Tat nicht erstellt und darf keine Bewerbungen annehmen (contactPersonOfGuteTatById())
		1.2 Die Bewerbung wurde bereits akzeptiert (db_getStatusOfBewerbung())
		1.3 Die Bewerbung wurde bereits abgelehnt (db_getStatusOfBewerbung())
		1.4 Es gibt keine Bewerbung von diesem Nutzer zu dieser Tat
		1.5 Anzahl der Helfer zu groß, um sie anzunehmen
		1.6 alles in Ordnung, der Nutzer kann die Bewerbung mit einem Text annehmen oder ablehnen
	3. Fall: Bewerbungsformular wurde abgeschickt.  Es wird eine Email an den Ersteller der Guten Tat gesendet mit dem Link zur Bewerbung (db_addBewerbung()). Es wird eine Bestätigung angezeigt und ein Link zur Detailseite der guten Tat. Außerdem wird ein Eintrag in der Datenbank vorgenommen
	4. Fall: Bewerbung-Annahme Formular wurde abgeschickt. Der Bewerber bekommt eine Anname-Email mit dem Link zur Detailseite der guten Tat 	(db_acceptBewerbung()) der Datenbankeintrag wird angepasst, ein neuer Eintrag in der Datenbank für die Helfer vorgenommen und eine Bestätigung + Plus Link zur Detailseite der Guten Tat wird angezeigt
	5. Fall: Bewerbung-Absage Formular wurde abgeschickt. Der Bewerber bekommt eine Absage-Email mit dem Link zur Detailseite der guten Tat
	(db_declineBewerbung()) der Datenbankeintrag wird angepasst und eine Bestätigung + LInk zur Detailseite der Guten Tat wird angezeigt
*/

echo '<h2>Bewerbung</h2>';

//TODO: Das hier muss im Link der Email gesetzt sein
//Fall 1: Bewerbung abschicken
if(isset($_GET['idGuteTat']) && !isset($_GET['candidateID'])) {
	$idGuteTat = $_GET['idGuteTat'];
	$idUser = $_USER->getID();

	if(!DBFunctions::db_doesGuteTatExists($idGuteTat)) {
		//Fall 1.1: Es existiert keine gute Tat mit der übergebenen ID
		echo '<h3><red>Die gute Tat konnte nicht gefunden werden :(</red></h3>';
	}
	else if(DBFunctions::db_isUserCandidateOfGuteTat($idGuteTat, $idUser)) {
		//Fall 1.2: Der Nutzer hatte sich bereits für diese Tat beworben
		echo '<h3><red>Du hast dich bereits für diese gute Tat beworben</red></h3>';
	}
	else if(DBFunctions::db_getUserIdOfContactPersonByGuteTatID($idGuteTat) === $idUser) {
		//Fall 1.3: Der Bewerber selbst hat die gute Tat erstellt
		echo '<h3><red>Du kannst dich nicht für eine gute Tat bewerben, die du selbst ausgeschieben hast</red></h3>';
	}
	else if(DBFunctions::db_getStatusOfGuteTatById($idGuteTat) === 'geschlossen') {
		//Fall 1.4: Die Gute Tat ist bereits geschlossen
		echo '<h3><red>Diese gute Tat wurde bereits abgeschlossen</red></h3>';
	}
	else {
		if(DBFunctions::db_isNumberOfAcceptedCandidatsEqualToRequestedHelpers($idGuteTat) === true) {
			//Fall 1.5: Die Anzahl der angenommenen Bewerbungen erfüllt die Anzahl der gewünschten Helfer
			echo '<h4>Hinweis: Diese gute Tat hat bereits genügend Helfer, du wirst u.U. als Reserve eingesetzt</h4>';
		}
		//Fall 1.6 (und 1.5): Nutzer darf die Bewerbung inkl. eines Bewerbungstextes abschicken

		$_SESSION['idGuteTat'] = $idGuteTat; //Zwischenspeichern, um nach dem Absenden darauf zugreifen zu können

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
	$status = DBFunctions::db_getStatusOfBewerbung($candidateID, $idGuteTat);
	if(DBFunctions::db_getUserIdOfContactPersonByGuteTatID($idGuteTat) != $idUser) {
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
	else if(DBFunctions::db_isNumberOfAcceptedCandidatsEqualToRequestedHelpers($idGuteTat) === true) {
		//Fall 1.5: Anzahl der angeforderten Helfer bereits erreicht, Bewerbung kann nicht angenommen werden, bleibt ausstehend
		echo '<h3><red>Die Anzahl der angeforderten Helfer für diese Tat wurde bereits erreicht. </p> Die Bewerbung muss u.U. später akzeptiert werden, falls ein Helfer absagt.</red></h3>';
	}
	else {
		//Fall 1.6: Bewerbung ist okay und kann angenommen oder abgelehnt werden inkl. einer Begründung.

		//Link zum Profil des Bewerbers

		echo '<a href="./profile?user='.DBFunctions::db_getUsernameOfBenutzerByID($candidateID).'">Zum Benutzer-Profil des Bewerbers</a><br><br>';

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
	$Bewerbungstext = $_POST['Bewerbungstext'];
	$idUser = $_USER->getID();
	$idGuteTat = $_SESSION['idGuteTat'];
	unset($_SESSION['idGuteTat']); //Zwischengespeicherte Variable lesen und anschließend löschen
	$UsernameOfBewerber = $_USER->getUsername();

	$NameOfGuteTat = DBFunctions::db_getNameOfGuteTatByID($idGuteTat);
	$UsernameOfErsteller = DBFunctions::db_getUsernameOfContactPersonByGuteTatID($idGuteTat);
	$MailOfErsteller = DBFunctions::db_getEmailOfContactPersonByGuteTatID($idGuteTat);

	//URL der Bewerbungsseite generieren
	$actual_link = $HOST."/deeds_bewerbung"."?idGuteTat=$idGuteTat&candidateID=$idUser";
	//$actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?idGuteTat=$idGuteTat&candidateID=$idUser";
	$MailSubject = "Neue Bewerbung für '$NameOfGuteTat'";
	//$MailContent = "$UsernameOfBewerber hat sich für deine gute Tat '$NameOfGuteTat' beworben. Er schreibt dazu: $Bewerbungstext Besuche die URL, um Details zur Bewerbung einzusehen $actual_link";

	$MailContent =
		"<div style=\"margin-left:10%;margin-right:10%;background-color:#757575\">
			<img src=\"img/wLogo.png\" alt=\"TueGutes\" title=\"TueGutes\" style=\"width:25%\"/>
		</div>
		<h2>Hallo $UsernameOfErsteller!</h2><br>
		<h3>$UsernameOfBewerber hat sich für deine gute Tat '$NameOfGuteTat' beworben. <br>
		<h3>Er schreibt dazu: \"$Bewerbungstext\"</h3><br>
		<h3>Besuche die <a href=\"$actual_link\">URL, um Details zur Bewerbung einzusehen</a></h3>";

	//Sende mail an Ersteller der guten Tat
	sendEmail($MailOfErsteller, $MailSubject, $MailContent);

	//Datenbank Eintrag
	DBFunctions::db_addBewerbung($idUser, $idGuteTat, $Bewerbungstext);
	//Bestätigung anzeigen
	echo '<h3><green>Deine Bewerbung wurde erfolgreich abgeschickt</green></h3>';
	echo '<a href="./deeds_details?id='.$idGuteTat.'">Zur \'Guten Tat\'</a>';

}
else if(isset($_POST['AnnehmenButton'])) {
	//Fall 4: Bewerbungs-Annahme Formular wurde abgeschickt
	$Begruendungstext = $_POST['Begruendungstext'];
	$idGuteTat = $_SESSION['idGuteTat']; //Zwischengespeicherte Variablen laden und anschließend löschen
	unset($_SESSION['idGuteTat']);
	$candidateID = $_SESSION['$candidateID'];
	unset($_SESSION['candidateID']);
	$UsernameOfErsteller = $_USER->getUsername();

	$MailOfBewerber = DBFunctions::db_getMailOfBenutzerByID($candidateID);
	$UsernameOfBewerber = DBFunctions::db_getUsernameOfBenutzerByID($candidateID);
	$NameOfGuteTat = DBFunctions::db_getNameOfGuteTatByID($idGuteTat);

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
	//+ neuer Datenbankeintrag in der Helper Relation
	DBFunctions::db_acceptBewerbung($candidateID, $idGuteTat, $Begruendungstext);

	//Bestätigung anzeigen
	echo '<h3><green>Der Bewerber wurde über die Annahme seiner Bewerbung informiert</green></h2>';
	echo '<a href="./deeds_details?id='.$idGuteTat.'">Zur \'Guten Tat\'</a>';
}
else if(isset($_POST['AblehnenButton'])) {
	//Fall 5: Bewerbung-Absage Formular wurde abgeschickt
	$Begruendungstext = $_POST['Begruendungstext'];
	$idGuteTat = $_SESSION['idGuteTat']; //Zwischengespeicherte Variablen laden und anschließend löschen
	unset($_SESSION['idGuteTat']);
	$candidateID = $_SESSION['$candidateID'];
	unset($_SESSION['candidateID']);
	$UsernameOfErsteller = $_USER->getUsername();

	$MailOfBewerber = DBFunctions::db_getMailOfBenutzerByID($candidateID);
	$UsernameOfBewerber = DBFunctions::db_getUsernameOfBenutzerByID($candidateID);
	$NameOfGuteTat = DBFunctions::db_getNameOfGuteTatByID($idGuteTat);

	$MailSubject = "Bewerbung abgelehnt!";
	$MailContent =
		"<div style=\"margin-left:10%;margin-right:10%;background-color:#757575\">
			<img src=\"img/wLogo.png\" alt=\"TueGutes\" title=\"TueGutes\" style=\"width:25%\"/>
		</div>
		<h2>Hallo $UsernameOfBewerber!</h2><br>
		<h3>Deine Bewerbung für die gute Tat '$NameOfGuteTat' wurde von $UsernameOfErsteller mit folgender Begründung abgelehnt:</h3> <br> \"$Begruendungstext\"";

	//Sende Absage-Mail an Bewerber
	sendEmail($MailOfBewerber, $MailSubject, $MailContent);

	//Datenbankeintrag in Application Relation anpassen
	DBFunctions::db_declineBewerbung($candidateID, $idGuteTat, $Begruendungstext);

	//Bestätigung anzeigen
	echo '<h3><green>Der Bewerber wurde über die Ablehnung seiner Bewerbung informiert</green></h2>';
	echo '<a href="./deeds_details?id='.$idGuteTat.'">Zur \'Guten Tat\'</a>';
}
else {
	//Die URL wurde mit ungültigen Argumenten aufgerufen
	echo '<h3><red>Der Bewerbungslink wurde mit ungültigen Argumenten aufgerufen</red></h3>';
}


require "./includes/_bottom.php";
?>
