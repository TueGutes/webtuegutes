<?php
/*
*@Autor Christian Hock, Klaus Sobotta, Nick Nolting, (refactored Henrik Huckauf), Alexander Gauggel
enthält Teile von deeds_create und deeds_bearbeiten
*/

require './includes/DEF.php';
require './includes/UTILS.php';
require './includes/db_connector.php';
require './includes/_top.php';
include "./includes/streets.php";

// Set all received values.
// Values belonging to page 1.
$name = isset($_GET['name']) ? $_GET['name'] : '';

// Values belonging to page 2.
$pictures = isset($POST['pictures']) ? $_POST['pictures'] : '';

// Values belonging to page 3.
$description = isset($_GET['description']) ? $_GET['description'] : '';

// Values belonging to page 4.
$street = isset($_GET['street']) ? $_GET['street'] : '';
$housenumber = isset($_GET['housenumber']) ? $_GET['housenumber'] : '';
$place = isset($_GET['place']) ? $_GET['place'] : '';
$starttime = isset($_GET['starttime']) ? $_GET['starttime'] : '';
$endtime = isset($_GET['endtime']) ? $_GET['endtime'] : '';
$organization = isset($_GET['organization']) ? $_GET['organization'] : '';
$countHelper = isset($_GET['countHelper']) ? $_GET['countHelper'] : '1';
$idTrust = isset($_GET['tat_verantwortungslevel']) ? $_GET['tat_verantwortungslevel'] : '';
$postalcode = isset($_GET['postalcode']) ? $_GET['postalcode'] : '';

// ALEX: Removed
//$category = isset($_GET['category']) ? $_GET['category'] : '';

// TODO: Move to Utils.php.
// Returns a map of postal code, place and house number to a given address.
function getPostalPlaceToAddress($pStreet, $pHouseNumber, $pPlace)
{
	$lRetVals = [
		"retPostal" => "",
		"retPlace" => "",
		"retHouseNumber" => "",
	];
	$lHouseNumber = (!is_numeric($pHouseNumber)) ? 1 : $pHouseNumber;

	// Create address in format "<street>[+<street appendices],<house number>,Hannover".
	$lAddressString = $pStreet . ',' . $pHouseNumber . ',' . $pPlace;
	// Replace empty spaces.
	$lAddressString = str_replace(' ', '+', $lAddressString);		
	// Get JSON result.
	$lContents = file('http://nominatim.openstreetmap.org/search?format=json&limit=2&q=' . $lAddressString);
	// Put string in new variable for safety.
	$lResult = $lContents[0];

	// If result string is too short, no address was found.
	if(strlen($lResult) < 10)
	{
		return $lRetVals;
	}

	$lContents = explode('"', $lResult);
		
	// Get the proper string containing postal code and place.
	$lFoundIndex = -1;
	for($i = 0; $i < sizeof($lContents); $i++)
	{
		if(stripos($lContents[$i], 'display_name') !== false)
		{
			$lFoundIndex = $i + 2;
			break;
		}
	}
	if($lFoundIndex === -1)
	{
		return $lRetVals;
	}
	// Put string in variable for safety.
	$lResult = $lContents[$lFoundIndex];
	$lContents = explode(',', $lResult);
	
	// Important for correctness.
	// OSM indices for place and postal code.
	if(sizeof($lContents) < 9)
	{
		$lIndexPlace = 2;
	}
	else
	{
		$lIndexPlace = 3;
	}
	$lIndexPostal = sizeof($lContents) - 2;

	$size = sizeof($lContents);
		
	// Set return values.
	$lRetVals['retPostal'] = $lContents[$lIndexPostal];
	$lRetVals['retPlace'] = $lContents[$lIndexPlace];

	// If housenumber was given, check if it was found.
	if(is_numeric($pHouseNumber))
	{
		if(is_numeric($lContents[0]))
		{
			$lRetVals['retHouseNumber'] = $lContents[0];
		}
		else if(is_numeric($lContents[1]))
		{
			$lRetVals['retHouseNumber'] = $lContents[1];
		}
	}
	return $lRetVals;
}

// =================================================================
// Beginn Seitenstruktur.

// Zeigt an auf welcher Unterseite man gerade ist.
isset($_GET['Seite']) ? $_GET['Seite'] : '';

if(isset($_GET['Seite'])){
	$Seite = $_GET['Seite'];
}
else {
	$Seite =1;
}

//Damit nur eine Seite aufgerufen wird
$stop='0';

// Variable für den "Weiter"-/"Zurück"-Button.
$button= isset($_GET['button']) ? $_GET['button'] : "0";


// Page 1. Set deed name.
if($Seite==1 || $Seite==2 ||($Seite=='3' && $button=='zurück')){
	//Guckt ob der Aufruf für Seite 2 erfolgreich war
	if($Seite==2) {	
		if(DBFunctions::db_doesGuteTatNameExists($name)){
			$Seite=1;
			$stop=1;
		}else if ($name === ''){
			$stop=2;
			$Seite=1;
		}
	}
	if($Seite==1 ||$Seite==3){

		echo'
		<h2>Wähle zuerst einen aussagekräftigen Namen für deine gute Tat</h2>
		<h3>Jede Tat hat einen eigenen Namen und kann auch durch diesen gesucht werden. Deine gute Tat wird dementsprechend öfter aufgerufen, wenn der Name intuitiv verständlich ist.</h3>
		<br><br>';
		
		//Fehlermeldung für nicht erfolgreichen Aufruf.
		if($stop=='1'){
			echo '<red>Eine andere gute Tat ist bereits unter diesem Namen registriert.</red>';
		}
		if($stop=='2'){
			echo '<red>Bitte einen Namen für die gute Tat eingeben.</red><br>';
		}
		// Alle buttons
		echo'
		<br><br>
			<form action="" method="GET">
				<br>
				<br>
				<input type="text" name="name" value="' . $name . '" placeholder="Name der Tat" required />
				<br>
				<br>
				<input type="hidden" name="Seite" value="' . 2 . '"required />
				<input type="submit" name="button" value="weiter" />
		</form>	';
	}
}

// Page 2. Set optional deed picture.
if($Seite==2 || ($Seite==4 && $button=='zurück')){

	$Seite==2;
	echo'
	<h2>Möchtest du ein Bild hochladen?</h2>
	<h3>Taten mit Bildern werden eher von anderen Nutzern angeklickt.</h3>
			

	<br><br>
	<div class="center block deeds_create">
			
	<br>
	<br>
	<form action="" method="POST" enctype="multipart/form-data">	
	<input type="file" name="pictures" accept="image/*">
	<input type="submit" name="button" value="hochladen" />
	<br>
	<br>';
	// ALEX: Altered.
	if(isset($_FILES['pictures']) && ($_FILES['pictures']['tmp_name'] != "")){
		$pictures='data: ' . mime_content_type($_FILES['pictures']['tmp_name']) . ';base64,' . base64_encode (file_get_contents($_FILES['pictures']['tmp_name'])); 
		echo '<h3><green>Das Bild ist nun hochgeladen</h2></green>
			<center><img src="' .  $pictures . '" ></center>
			<br>
			<br>
			<br>';
	}
			
	echo'
		</form>
		<form action="" method="GET" enctype="multipart/form-data">	
		<input type="hidden" name="Seite" value="3"required />		
		<input type="hidden" name="name" value="' . $name . '"required />
		<input type="hidden" name="pictures" value="' . $pictures . '"required />
		<input type="submit" name="button" value="zurück" />
		<input type="submit" name="button" value="weiter" />
		</form>	';
}

// Page 3. Set deed description.
if((($Seite==3 || $Seite==4)&& $button=='weiter' )||(($Seite==5)&& $button=='zurück')){	
	if($Seite==4){
		if ($description === ''){
			$stop=1;
			$Seite=3;
			
		}
	}
	if($Seite==3 || $Seite==5){
		if($stop==1){
			echo '<h3><red>Bitte eine neue Beschreibung eingeben.</red></h3><br>';
		}
		echo'
			<h2>Beschreibe deine Tat.</h2>
			<h3>Was und wie soll das vorhaben durchgeführt werden? </h3>
			<br><br>
			<div class="center block deeds_create">
			<form action="" method="GET" enctype="multipart/form-data">
			<br>
			<br>
			<textarea id="text" name="description" rows="10" placeholder="Beschreibe die auszuführende Tat. So kannst du für dein Angebot werben." required>'
			. $description . '</textarea>
				<br><br>
				<br>
				
				<input type="hidden" name="Seite" value="4"required />
				<input type="hidden" name="name" value="' . $name . '"required />
				<input type="hidden" name="pictures" value="' . $pictures . '"required />
				<input type="submit" name="button" value="zurück" />
				<input type="submit" name="button" value="weiter" />
		</form>	';
		} 
}

// Page 4. Set additional deed infos.
if(($Seite==4 ||$Seite==5)&& (($button=='weiter') || ($button == 'absenden') ))
{	
	// ALEX: Created string for error messages.
	$errorMessage = "";
	$hasCompleteAddress = true;
	$lDatesCorrect = true;
	
	// ALEX: Moved checks here.
	if($Seite == 5)
	{
		if(!isset($_GET['street']) || ($street == ""))
		{
			$errorMessage .= "Bitte eine Straße angeben.<br>";
			$hasCompleteAddress = false;
		}
		
		// Check street value for digits. (Could be solved better probably.)
		for($i = 0; $i < sizeof($street); $i++)
		{
			if(is_numeric($street[$i]))
			{
				$errorMessage .= "Die Hausnummer bitte getrennt angeben.";
				break;
			}
		}
		
		if(!isset($_GET['housenumber']) || ($housenumber == ""))
		{
			$errorMessage .= "Bitte eine Hausnummer angeben.";
			$hasCompleteAddress = false;
		}
		// TODO: Check for house number additions.
		/*else if(!is_numeric($housenumber))
		{
			$errorMessage .= "Bitte eine gültige Hausnummer angeben.";
		}*/
		
		if(!isset($_GET['place']) || ($place == ""))
		{
			$errorMessage .= "Bitte einen Ort angeben.";
			$hasCompleteAddress = false;
		}
				
		//Startzeitpunkt
		$start_dh = (new DateHandler())->set($starttime);
		if(!$start_dh)
		{
			$errorMessage .= "Bitte ein gültiges Startdatum angeben.<br>";
			$lDatesCorrect = false;
		}
		
		//Endzeitpunkt
		$end_dh = (new DateHandler())->set($endtime);
		if(!$end_dh)
		{
			$errorMessage .= "Bitte ein gültiges Enddatum angeben.<br>";
			$lDatesCorrect = false;
		}
		
		
		// TODO: Enddatum darf nicht vor dem Startdatum liegen
		/*if($lDatesCorrect && ( <CHECK> )
		{
			$errorMessage .= "Das Startdatum muss vor dem Enddatum liegen.<br>";
		}*/

		//Anzahl Helfer keine Zahl
		if(!is_numeric($countHelper))
		{
			$errorMessage .= "Bitte eine Zahl bei der Anzahl der Helfer angeben.<br>";
		}
		
		// ALEX: Modified.
		if((!isset($_GET['organization'])) || ($_GET['organization'] == ""))
		{
			$errorMessage .= "Bitte eine Organisation angeben.";
		}
		
		// Important: Has to be the last check to avoid multiple DB inserts.
		if($hasCompleteAddress && ($errorMessage == ""))
		{
			// Check for valid street and house number.
			$lFoundValues = getPostalPlaceToAddress($street, $housenumber, $place);
			
			$lAddMailContent = "";
			
			if($lFoundValues['retPostal'] == "")
			{
				$errorMessage .= "Diese Adresse wurde nicht gefunden. Hat sich vielleicht ein Schreibfehler eingeschlichen?";
			}
			else
			{
				// Has to be set to search it in DB when finishing creating deed.
				$postalcode = $lFoundValues['retPostal'];
				
				if($lFoundValues['retHouseNumber'] == "")
				{
					$lAddMailContent = "<br>Die Hausnummer " . $housenumber . " der Straße " . $street . " wurde nicht gefunden. Bitte prüfen.";
				}
			}			
		}
	}
	
	// ALEX: Modified.
	if(($stop != 0) || $errorMessage !== "")
	{
		$Seite=4;
	}
	
	if($Seite==4)
	{
		echo'
		<h2>Hier noch ein paar zusätzliche Informationen</h2>
		<h3>Wir benötigen noch die Adresse des Ortes, wo die gute Tat durchgeführt werden soll sowie ein paar Angaben zu den Helfern.</h3>
		<br><br>
		<div class="center block deeds_create">
			<form action="" method="GET" enctype="multipart/form-data">';
		
		// ALEX: If any errors occured, print them.
		echo '<red>' . $errorMessage . '</red>';
		
		// ALEX: Added street list.
		echo '<br><br>
		<div class="center block deeds_create">
		<form action="" method="GET" enctype="multipart/form-data">
		<br>
		<br>
		<br>
		<input type="search" list="lstStreets" name="street" value="' . $street . '" placeholder="Strasse" required />'
			. $streetList . '<input type="text" name="housenumber" value="' . $housenumber . '" placeholder="Hausnummer" required />
		<br>
		<input type="text" name="place" value="' . $place . '" placeholder="Ort" required/>
		<br>
		<input type="date" name="starttime" value="' . $starttime . '" placeholder="Startzeitpunkt" required />
		<br>
		<input type="date" name="endtime" value="' . $endtime . '" placeholder="Endzeitpunkt" required />
		<br>
		<input type="text" name="organization" value="' . $organization . '" placeholder="Organisation" />
		<br>
		Benötigte Helfer:<br>
		<input type="number" name="countHelper" value="' . $countHelper . '" placeholder="Benötigte Helfer" required />
		<br>
		Erforderlicher Verantwortungslevel:
		<br>
		<select name="tat_verantwortungslevel">
			<option value="1"<?php echo $idTrust == 1?" selected":""; >1</option>
			<option value="2"<?php echo $idTrust == 2?" selected":""; >2</option>
			<option value="3"<?php echo $idTrust == 3?" selected":""; >3</option>
		</select>
		<br><br>
		<br><br>
		<br>
		<input type="hidden" name="name" value="' . $name . '"required />
		<input type="hidden" name="pictures" value="' . $pictures . '"required />
		<input type="hidden" name="description" value="' . $description . '"required />
		<input type="hidden" name="Seite" value="5"required />
		<input type="submit" name="button" value="zurück" />
		<input type="submit" name="button" value="absenden" />
		</form>';
	}
}

// Page 5. Save deed if valid.
if(($Seite==5) && ($button!='zurück'))
{					
	if(!DBFunctions::db_doesGuteTatNameExists($name))
	{
		// ALEX: Set category temporary to "keine Angabe".
		$category = "keine Angabe";
	
		// If postal code wasn't found in DB, add it and set foreign key in userdata.
		$lIdPostal = DBFunctions::db_getIdPostalbyPostalcodePlace($postalcode, $place);
		
		// If no corresponding postal code was found, add it to DB.
		if($lIdPostal == "")
		{
			DBFunctions::db_insertPostalCode($postalcode, $place);
			$IdPostal = DBFunctions::db_getIdPostalbyPostalcodePlace($postalcode, $place);
		}
	
		//Einfügen der Guten Tat
		$uid = DBFunctions::db_idOfBenutzername($_USER->getUsername());
							
		/* echo $name . ', ' . $uid . ', ' . $category . ', ' . $street . ', '. $housenumber . ', ' . $lIdPostal . ', ' . $start_dh->get() . ', ' . $end_dh->get() . ', ' . $organization . ', ' . $countHelper . ', ' .  $idTrust . ', ' . $description . ', ' . $pictures; */
		DBFunctions::db_createGuteTat($name, $uid, $category, $street, $housenumber, 
									  $lIdPostal, $start_dh->get(),$end_dh->get(), $organization, $countHelper,
									  $idTrust, $description, $pictures);
		
		//Versenden der Info-Mails
		
		//Bestimmen der Empfänger
		$mods = DBFunctions::db_getAllModerators();
		$admins = DBFunctions::db_getAllAdministrators();

		//Festlegen des Mail-Inhalts
		$mailSubject = 'Gute Tat ' . "'" . $_GET['name'] . "'" . ' wurde erstellt!';
		$mailContent1 = '<div style="margin-left:10%;margin-right:10%;background-color:#757575"><img src="img/wLogo.png" alt="TueGutes" title="TueGutes" style="width:25%"/></div><h2>Hallo!';
		$mailContent2 = '</h2><br>' . $_USER->getUsername() . ' hat gerade eine neue gute Tat erstellt. <br>Um die gute Tat zu bestätigen oder abzulehnen, klicke auf den folgenden Link: <br><a href="' . $HOST . '/deeds_details?id=' . DBFunctions::db_getIDOfGuteTatByName($_GET['name']) . '">Zur guten Tat</a>';
	
		// ALEX: Added additional info text if street wasn't found.
		$mailContent2 .= $lAddMailContent;

		//Versenden der Emails an Moderatoren
		for ($i = 0; $i < sizeof($mods); $i++) {
			sendEmail($mods[$i], $mailSubject, $mailContent1 . $mailContent2);
		}
		//Versenden der Emails an Administratoren
		for ($i = 0; $i < sizeof($admins); $i++) {
			sendEmail($admins[$i], $mailSubject, $mailContent1 . $mailContent2);
		}
		// ALEX: Moved here because this text should only be displayed if deed was created.
		echo'
		<h2><green>Deine Tat wurde erstellt! </green></h2>
		<h3>und wird nun von uns geprüft. </h3>
		<a href="./deeds.php"><input type="button" name="Toll" value="Zurück zur Übersicht"/></a>';
	}
	else
	{
		echo 
			'<h2>Ups...</h2>
			<h3>Diese gute Tat wurde bereits hinzugefügt.
			<br>
			Bitte kontaktiere gegebenenfalls einen Moderator.</h3>
			<a href="./deeds.php"><input type="button" name="back" value="Zurück zur Übersicht"/></a>';
	}
}
require './includes/_bottom.php';
?>



<?php
/*
Die alte Datei zum erstellen von guten Taten
*@author Christian Hock, Klaus Sobotta, Nick Nolting (refactored Henrik Huckauf)
*Verlinkung zu Orten fehlt
*Kategorie soll editierbar sein
*
*DateHandler eingebunden | Henrik Huckauf
*/
/*
require './includes/DEF.php';
require './includes/UTILS.php';

require './includes/db_connector.php';

require './includes/_top.php';


$name = isset($_POST['name']) ? $_POST['name'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';
$category = isset($_POST['category']) ? $_POST['category'] : '';
$street = isset($_POST['street']) ? $_POST['street'] : '';
$housenumber = isset($_POST['housenumber']) ? $_POST['housenumber'] : '';
$postalcode = isset($_POST['postalcode']) ? $_POST['postalcode'] : '';
$place = isset($_POST['place']) ? $_POST['place'] : '';
$starttime = isset($_POST['starttime']) ? $_POST['starttime'] : '';
$endtime = isset($_POST['endtime']) ? $_POST['endtime'] : '';
$organization = isset($_POST['organization']) ? $_POST['organization'] : '';
$countHelper = isset($_POST['countHelper']) ? $_POST['countHelper'] : '1';
$idTrust = isset($_POST['tat_verantwortungslevel']) ? $_POST['tat_verantwortungslevel'] : '';

$output = '';
if(isset($_POST['set']) && $_POST['set'] == '1')
{
	//TIMM: Übergangsweise wegen DB funktion
	$pictures= '';

	/*
	0-> gerade erst erstellt 
	1-> bewilligt
	2-> warte auf Antwort
	3-> abgelehnt
	*/
	//$status = 0; 
/*
	$error = false;

	//Name der guten Tat
	if(empty($name))
	{
		$output .= '<red>Es wurde kein Name für die gute Tat vergeben.</red>';
		$error = true;
	}

	//Falls eine fehlerhafte PLZ angegeben wird
	if(!is_numeric($postalcode))
	{
		$output .= '<red>Bitte Postleitzahl überprüfen! Als Postleitzahl sind nur fünfstellige Zahlen erlaubt.</red>';
		$error = true;
	}

	//TODO Enddatum darf nicht vor dem Startdatum liegen
	
	//Startzeitpunkt
	$start_dh = (new DateHandler())->set($starttime);
	if(!$start_dh)
	{
		$output .= '<red>Es wurde kein gültiger Startzeitpunkt für die gute Tat festgelegt.</li>';
		$error = true;
	}
	//Endzeitpunkt
	$end_dh = (new DateHandler())->set($endtime);
	if(!$end_dh)
	{
		$output .= '<red>Es wurde kein gültiger Endzeitpunkt für die gute Tat festgelegt.</red>';
		$error = true;
	}

	if(!DBFunctions::db_getIdPostalbyPostalcodePlace($postalcode, $place))
	{
		$output .= '<red>Derzeit sind leider nur Adressen in Hannover möglich. Deine Adresse wird nicht angenommen?<br><a href="./contact">Kontaktiere uns hier</a></red>';
		$error = true;
	}

	//Anzahl Helfer keine Zahl
	if(!is_numeric($countHelper))
	{
		$output .= '<red>Bitte Anzahl der Helfer überprüfen! Als Anzahl muss eine einfache Zahl eingegeben werden.</red>';
		$error = true;
	}

	if(!$error)
	{
		//Einfügen der Guten Tat
		$uid = DBFunctions::db_idOfBenutzername($_USER->getUsername());
		$plz = DBFunctions::db_getIdPostalbyPostalcodePlace($postalcode, $place);
		DBFunctions::db_createGuteTat($name, $uid, $category, $street, $housenumber, 
									  $plz, $start_dh->get(),$end_dh->get(), $organization, $countHelper,
									  $idTrust, $description, $pictures);

		//Versenden der Info-Mails
		
		//Bestimmen der Empfänger
		$mods = DBFunctions::db_getAllModerators();
		$admins = DBFunctions::db_getAllAdministrators();

		//Festlegen des Mail-Inhalts
		$mailSubject = 'Gute Tat ' . "'" . $_POST['name'] . "'" . ' wurde erstellt!';
		$mailContent1 = '<div style="margin-left:10%;margin-right:10%;background-color:#757575"><img src="img/wLogo.png" alt="TueGutes" title="TueGutes" style="width:25%"/></div><h2>Hallo!';
		$mailContent2 = '</h2><br>' . $_USER->getUsername() . ' hat gerade eine neue gute Tat erstellt. <br>Um die gute Tat zu bestätigen oder abzulehnen, klicke auf den folgenden Link: <br><a href="' . $HOST . '/deeds_details?id=' . DBFunctions::db_getIDOfGuteTatByName($_POST['name']) . '">Zur guten Tat</a>';

		//Versenden der Emails an Moderatoren
		for ($i = 0; $i < sizeof($mods); $i++) {
			sendEmail($mods[$i], $mailSubject, $mailContent1 . $mailContent2);
		}
		//Versenden der Emails an Administratoren
		for ($i = 0; $i < sizeof($admins); $i++) {
			sendEmail($admins[$i], $mailSubject, $mailContent1 . $mailContent2);
		}


		$output = '<green>Ihre Tat wurde erfolgreich erstellt und wird nun von uns geprüft.</green>';
		
		$name = '';
		$description = '';
		$category = '';
		$street = '';
		$housenumber = '';
		$postalcode = '';
		$place = '';
		$starttime = '';
		$endtime = '';
		$organization = '';
		$countHelper = '1';
		$idTrust = '';
	}
}
?>	

<h2>Eine Tat erstellen</h2>

<div id='output'><?php echo $output; ?></div>
<br><br>
<div class="center block deeds_create">
	<form action="" method="post">
		<h3>Bitte alle Felder ausfüllen =)</h3>
		<br>
		<br>
		<input type="text" name="name" value="<?php echo $name; ?>" placeholder="Name der Tat" required />
		<br>
		<textarea id="text" name="description" rows="10" placeholder="Beschreiben Sie die auszuführende Tat. Werben Sie für Ihr Angebot. Nutzen sie ggf. eine Rechtschreibüberprüfung." required><?php echo $description; ?></textarea>
		<br><br>
		Kategorie:<br>
		<select name="category">
			<option value="Andere">Andere</option>
			<option value="Altenheim">Altenheim</option>
			<option value="Busbahnhof">Busbahnhof</option>
			<option value="Mülleinsammeln">Müll einsammeln</option>
			<?php
			//TODO get set from db 
			//loop set echo options reselect prev option if $category is set
			?>
		</select>
		<br>
		<input type="submit" value="neue Kategorie vorschlagen" form="suggestCategory" />
		<br>
		
		<!--TODO kategorie vorschlagen-->
		<br>
		<input type="text" name="street" value="<?php echo $street; ?>" placeholder="Straßenname" required />
		<input type="text" name="housenumber" value="<?php echo $housenumber; ?>" placeholder="Hausnummer" required />
		<br>
		<input type="text" name="postalcode" value="<?php echo $postalcode; ?>" placeholder="Postleitzahl" required />
		<br>
		<input type="text" name="place" value="<?php echo $place; ?>" placeholder="Stadtteil" required />
		<br>
		<input type="text" name="starttime" value="<?php echo $starttime; ?>" placeholder="Startzeitpunkt (dd.mm.yyyy HH:MM)" required />
		<br>
		<input type="text" name="endtime" value="<?php echo $endtime; ?>" placeholder="Endzeitpunkt (dd.mm.yyyy HH:MM)" required />
		<br>
		<input type="text" name="organization" value="<?php echo $organization; ?>" placeholder="Organisation" />
		<br>
		Benötigte Helfer:<br>
		<input type="number" name="countHelper" value="<?php echo $countHelper; ?>" placeholder="Benötigte Helfer" required />
		<br>
		Erforderlicher Verantwortungslevel:<br>
		<select name="tat_verantwortungslevel">
			<option value="1"<?php echo $idTrust == 1?' selected':''; ?>>1</option>
			<option value="2"<?php echo $idTrust == 2?' selected':''; ?>>2</option>
			<option value="3"<?php echo $idTrust == 3?' selected':''; ?>>3</option>
		</select>
		<br><br>
		<input type='hidden' name='set' value='1' />
		<input type="submit" name="button" value="Tat erstellen" />
		<br><br>
		<a href="./deeds"><input type="button" name="button" value="Zurück" /></a>
	</form>
	
	<form id="suggestCategory" action="./contact" method="post">
		<input type='hidden' name='suggestCategory' value='1' />
		<input type='hidden' name='message' value='Ich vermisse folgende Kategorie beim Erstellen einer guten Tat: ' />
	</form>
</div>

<?php
require './includes/_bottom.php';
*/?>