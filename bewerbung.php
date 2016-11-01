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
			  und seine Bewerbung abschicken, danach wird er auf die Detailseite der Tat weitergeleitet. Es wird eine Email an den Ersteller der Guten Tat gesendet mit dem Link zur Bewerbung (addBewerbung())
	2. Ein Nutzer schaut sich die Bewerbung eines anderen Benutzers an isset $_GET['tatID'] && $_GET['userID']
		1.1 Der Benutzer hat die Tat nicht erstellt und darf keine Bewerbungen annehmen (contactPersonOfGuteTatById())
		1.2 Die Bewerbung wurde bereits akzeptiert (statusOfBewerbung())
		1.3 Es gibt keine Bewerbung von diesem Nutzer zu dieser Tat
		1.4 alles in Ordnung, der Nutzer kann die Bewerbung mit einem Text annehmen oder ablehnen, wird auf die Detailseite weitergeleitet und der Bewerber bekommt eine Email mit dem Link zur Detailseite der guten Tat (acceptBewerbung())
		1.* Anzahl der Helfer zu groß, um sie anzunehmen	
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
		//Fall 1.6 (und 1.5): Nutzer kann Bewerbung abschicken und wird anschließend auf Detailseite der Guten Tat weitergeleitet
		
	}
	
}
//Fall 2: Bewerbung annehmen oder ablehnen
elseif(isset($_GET['idGuteTat']) && isset($_GET['candidateID'])) {
	$idGuteTat = $_GET['idGuteTat'];
	$candidateID = $_GET['candidateID'];
	$userID = $_SESSION['user'];
	if(contactPersonOfGuteTatById($idGuteTat) != $userID) {
		//Fall 1.1: Der Nutzer hat die gute Tat nicht erstellt und darf dementsprechend ihre Bewerbungen nicht annehmen
		echo '<h3><red>Du darfst nur Bewerbungen zu guten Taten einsehen, die du selbst erstellt hat</red></h3>'; 
	}
	elseif() {
		
		
	}
	
	
}
else {
	//Die URL wurde ohne Argumente aufgerufen
	echo '<h3><red>Der Bewerbungslink wurde mit ungültigen Argumenten aufgerufen</red></h3>';
}


require "./includes/_bottom.php"; 
?>