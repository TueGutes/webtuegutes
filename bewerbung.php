<?php
//Author: Andreas Blech
/*Description: Auf dieser Seite werden zwei Szenarien behandelt.
* 1. Ein Nutzer bewirbt sich für eine gute Tat eines anderen Nutzers
  2. Ein Nutzer schaut sich die Bewerbung eines anderen Nutzers für seine gute Tat an und kann diese annehmen oder ablehnen
*/
session_start();

//Prüfung, ob der Nutzer sich bereits eingeloggt hat.
if(!(isset($_SESSION['loggedIn']))) {
	$_SESSION['loggedIn'] = false;
}

require "./includes/_top.php";

//Inkludieren von script-Dateien
include './includes/db_connector.php';
include './includes/emailSender.php';


/*
DB Funktionen:
	doesGuteTatExists()
	isUserCandidateOfGuteTat()
	contactPersonOfGuteTatById()
	statusOfGuteTatById()
	isNumberOfAcceptedCandidatsEqualToRequestedHelpers()
	addBewerbung($userID, $tatID, $promotion);

	statusOfBewerbung($userID, $tatID) //Oder false, falls keine Bewerbung vorliegt
	acceptBewerbung($userID, $tatID, $explanation)
	declineBewerbung($userID, $tatID, $explanation)
*/


/*
Es gibt ... Fälle:
	0. Der Nutzer ist nicht eingeloggt und darf sich keine Bewerbungen anschauen
	1. Ein Nutzer bewirbt sich für eine gute Tat (isset $_GET['tatID'])
		1.1 Es existiert keine gute Tat zu dieser ID (doesGuteTatExists())
		1.2 Der Nutzer hatte sich bereits für diese Tat beworben (isUserCandidateOfGuteTat()
		1.3 Der Nutzer selbst hat die Tat erstellt (contactPersonOfGuteTatById())
		1.4 Die gute Tat wurde bereits abgeschlossen (statusOfGuteTatById())
		1.5 Die Anzahl der angenommenen Bewerbungen ist gleich der Anzahl der benötigten Helfer (isNumberOfAcceptedCandidatsEqualToRequestedHelpers()) : Hinweis: Man ist nur Reserve, aber bewerben kann man sich trotzdem
		1.6 alles in Ordnung, der Nutzer kann sich mit einem Bewerbungstext bewerben
			  und seine Bewerbung abschicken.
	2. Ein Nutzer schaut sich die Bewerbung eines anderen Benutzers an isset $_GET['tatID'] && $_GET['userID']
		1.1 Der Benutzer hat die Tat nicht erstellt und darf keine Bewerbungen annehmen (contactPersonOfGuteTatById())
		1.2 Die Bewerbung wurde bereits akzeptiert (statusOfBewerbung())
		1.3 Die Bewerbung wurde bereits abgelehnt (status Of Bewerbung())
		1.4 Es gibt keine Bewerbung von diesem Nutzer zu dieser Tat
		1.5 Anzahl der Helfer zu groß, um sie anzunehmen
		1.6 alles in Ordnung, der Nutzer kann die Bewerbung mit einem Text annehmen oder ablehnen
	3. Fall: Bewerbungsformular wurde abgeschickt.  Es wird eine Email an den Ersteller der Guten Tat gesendet mit dem Link zur Bewerbung (addBewerbung()). Es wird eine Bestätigung angezeigt und ein Link zur Detailseite der guten Tat. Außerdem wird ein Eintrag in der Datenbank vorgenommen
	4. Fall: Bewerbung-Annahme Formular wurde abgeschickt. Der Bewerber bekommt eine Anname-Email mit dem Link zur Detailseite der guten Tat 	(acceptBewerbung()) der Datenbankeintrag wird angepasst, ein neuer Eintrag in der Datenbank für die Helfer vorgenommen und eine Bestätigung + Plus Link zur Detailseite der Guten Tat wird angezeigt
	5. Fall: Bewerbung-Absage Formular wurde abgeschickt. Der Bewerber bekommt eine Absage-Email mit dem Link zur Detailseite der guten Tat
	(declineBewerbung()) der Datenbankeintrag wird angepasst und eine Bestätigung + LInk zur Detailseite der Guten Tat wird angezeigt
*/
//Fall 0: Profile sind nur für eingeloggte Nutzer sichtbar:
if (!@$_SESSION['loggedIn']) die ('<h3>Bewerbungen sind nur für eingeloggte Nutzer sichtbar!</h3><p/><a href="login.php">Zum Login</a>');

echo '<h2>Bewerbung</h2>';

//TODO: Das hier muss im Link der Email gesetzt sein
//Fall 1: Bewerbung abschicken
if(isset($_GET['idGuteTat']) && !isset($_GET['candidateID'])) {
	$idGuteTat = $_GET['idGuteTat'];
	$userID = $_SESSION['user'];

	if(!doesGuteTatExists($idGuteTat)) {
		//Fall 1.1: Es existiert keine gute Tat mit der übergebenen ID
		echo '<h3><red>Die gute Tat konnte nicht gefunden werden :(</red></h3>';
	}
	elseif(isUserCandidateOfGuteTat($idGuteTat, $userID)) {
		//Fall 1.2: Der Nutzer hatte sich bereits für diese Tat beworben
		echo '<h3><red>Du hast dich bereits für diese Gute Tat beworben</red></h3>';
	}
	elseif(contactPersonOfGuteTatById($idGuteTat) === $userID) {
		//Fall 1.3: Der Bewerber selbst hat die gute Tat erstellt
		echo '<h3><red>Du kannst dich nicht für eine gute Tat bewerben, die du selbst ausgeschieben hast</red></h3>';
	}
	elseif(statusOfGuteTatById() === 'geschlossen') {
		//Fall 1.4: Die Gute Tat ist bereits geschlossen
		echo '<h3><red>Diese gute Tat wurde bereits abgeschlossen</red></h3>';
	}
	else {
		if(isNumberOfAcceptedCandidatsEqualToRequestedHelpers() === true) {
			//Fall 1.5: Die Anzahl der angenommenen Bewerbungen erfüllt die Anzahl der gewünschten Helfer
			echo '<h4>Hinweis: Diese gute Tat hat bereits genügend Helfer, du wirst u.U. als Reserve eingesetzt</h4>';
		}
		//Fall 1.6 (und 1.5): Nutzer darf die Bewerbung inkl. eines Bewerbungstextes abschicken

	}

}
//Fall 2: Bewerbung annehmen oder ablehnen
elseif(isset($_GET['idGuteTat']) && isset($_GET['candidateID'])) {
	$idGuteTat = $_GET['idGuteTat'];
	$candidateID = $_GET['candidateID'];
	$userID = $_SESSION['user'];
	$status = statusOfBewerbung($userID, $tatID);
	if(contactPersonOfGuteTatById($idGuteTat) != $userID) {
		//Fall 1.1: Der Nutzer hat die gute Tat nicht erstellt und darf dementsprechend ihre Bewerbungen nicht annehmen
		echo '<h3><red>Du darfst nur Bewerbungen zu guten Taten einsehen, die du selbst erstellt hat</red></h3>';
	}
	elseif($status === "angenommen") {
		//Fall 1.2: Die Bewerbung wurde bereits angenommen
		echo '<h3><red>Die Bewerbung wurde bereits angenommen - der Bewerber wurde informiert</red></h3>';
	}
	elseif($status === "abgelehnt") {
		//Fall 1.3: Die Bewerbung wurde bereits abgelehnt
		echo '<h3><red>Die Bewerbung wurde bereits abgelehnt - der Bewerber wurde informiert</red></h3>';
	}
	elseif($status === false) {
		//Fall 1.4: Es existiert keine Bewerbung dieses Nutzers für die gute Tat (sollte Websitetechnisch niemals passieren)
		echo '<h3><red>Es existiert keine Bewerbung des Bewerbers für die Tat</red></h3>';
	}
	elseif(isNumberOfAcceptedCandidatsEqualToRequestedHelpers() === true) {
		//Fall 1.5: Anzahl der angeforderten Helfer bereits erreicht, Bewerbung kann nicht angenommen werden, bleibt ausstehend
		echo '<h3><red>Die Anzahl der angeforderten Helfer für diese Tat wurde bereits erreicht. </p> Die Bewerbung muss u.U. später akzeptiert werden, falls ein Helfer absagt.</red></h3>';
	}
	// else {
		//Fall 1.6: Bewerbung ist okay und kann angenommen oder abgelehnt werden inkl. einer Begründung.

	}
}
elseif() {
	//Fall 3: Bewerbungsformular wurde abgeschickt
	//TODO: Sende mail an Ersteller der guten Tat
	//TODO: Datenbank Eintrag
	//TODO: Anzeige von Bestätigung
	//TODO: Link zu Detailseite der guten Tat

}
elseif() {
	//Fall 4: Bewerbungs-Annahme Formular wurde abgeschickt
	//TODO: Sende Mail an Bewerber
	//TODO: Datenbankeintrag anpassen
	//TODO: neuer Datenbankeintrag in der Helper Relation
	//TODO: Bestätigung anzeigen
	//TODO: Link zu Detailseite der guten Tat

}
elseif() {
	//Fall 5: Bewerbung-Absage Formular wurde abgeschickt
	//TODO: Sende Absage-Mail an Bewerber
	//TODO: Datenbankeintrag anpassen
	//TODO: Bestätigung anzeigen
	//TODO: Link zu Detailseite der guten Tat

}
else {
	//Die URL wurde ohne Argumente aufgerufen
	echo '<h3><red>Der Bewerbungslink wurde mit ungültigen Argumenten aufgerufen</red></h3>';
}


require "./includes/_bottom.php";
?>
