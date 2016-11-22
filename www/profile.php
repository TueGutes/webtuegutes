<!--Formular zum Vorschlagen einer neuen Adresse-->
<form id="suggestAddress" action="./contact" method="post">
	<input type='hidden' name='suggestCategory' value='1' />
	<input type='hidden' name='message' value='Ich vermisse folgende Adresse: ' />
</form>

<?php
/*
* @author: Nick Nolting, Alexander Gauggel
*/

	// Funktionen
	// ALEX 
	// Returns the day of birth of a given date in format "YYYY-MM-DD".
	function getDayOfBirth($pBirthValues)
	{
		return substr($pBirthValues, 8, 2);
	}
	// Returns the month of birth of a given date in format "YYYY-MM-DD".
	function getMonthOfBirth($pBirthValues)
	{
		return substr($pBirthValues, 5, 2);
	}
	// Returns the year of birth of a given date in format "YYYY-MM-DD".
	function getYearOfBirth($pBirthValues)
	{
		return substr($pBirthValues, 0, 4);
	}

	// ALEX: Inserts a new postal code.
	// TIMM: Ausgelagert in db_connector.
	// hat auch db_ davor jetzt: db_insertPostalCode
	

	// Returns a map of postal code and place to a given address.
	function getPostalPlaceToAddress($pStreet, $pHouseNumber)
	{
		// ALEX2: Added entry retHouseNumber.
		$lRetVals = [
			"retPostal" => "",
			"retPlace" => "",
			"retHouseNumber" => "",
		];
		// ALEX2: Added value $lHouseNumber to set a valid search value if neccessary.
		$lHouseNumber = (!is_numeric($pHouseNumber)) ? 1 : $pHouseNumber;
		
		// Create address in format "<street>[+<street appendices],<house number>,Hannover".
		$lAddressString = $pStreet . ',' . $pHouseNumber . ',Hannover';
		// Replace empty spaces.
		$lAddressString = str_replace(' ', '+', $lAddressString);
		// Get JSON result.
		$lContents = file('http://nominatim.openstreetmap.org/search?format=json&limit=2&q=' . $lAddressString);
		// Put string in new variable for safety.
		$lResult = $lContents[0];
		
		// TODO: Raus
		//echo $lResult;
		//echo strlen($lResult);
		
		// ALEX2: If result string is too short, no address was found.
		if(strlen($lResult) < 10)
		{
			return $lRetVals;
		}
		
		$lContents = explode('"', $lResult);
		// Get the proper string containing postal code and place.
		$lFoundIndex = -1;
		for($i = 0; $i < sizeof($lContents); $i++)
		{
			if(stripos($lContents[$i], 'Hannover') !== false) 
			{
				$lFoundIndex = $i;
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
		
		// ALEX2: If housenumber was given, check if it was found.
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
	
	// ALEX2: Checks whether the given value is a number and if yes, is it out of the given range.
	function checkNumberAndRange($pValue, $pMin, $pMax)
	{
		// Not a number?
		if(!is_numeric($pValue))
		{
			return false;
		}
		// Out of range?
		if(($pValue < $pMin) || ($pValue > $pMax))
		{
			return false;
		}
		return true;
	}

	//Includes
	require "./includes/DEF.php";
	
	include "./includes/db_connector.php";
	include "./includes/Map.php";
	require "./includes/_top.php";

	// ALEX
	include "./includes/streets.php";
	// ALEX2
	// Darf hier nicht erneut eingebunden werden, da dies schon in "DEF.php" geschieht.
	//include "./includes/mail.php";

	//Profile sind nur für eingeloggte Nutzer sichtbar:
	if (!$_USER->loggedIn()) die ('Profile sind nur für eingeloggte Nutzer sichtbar!<p/><a href="./login">Zum Login</a>');	

	//Sollte das Profil gelöscht werden?
	if (isset($_POST['save_pw']))
		DBFunctions::db_delete_user($_USER->getUsername(), $_POST['save_pw']);

	//Festlegen des auszulesenden Nutzers:
	if (!isset($_GET['user'])) $_GET['user'] = $_USER->getUsername();
	$thisuser = DBFunctions::db_get_user($_GET['user']);

	//Festlegen der Sichtbarkeitseinstellungen
	if (strtoupper($_USER->getUsername())===strtoupper($thisuser['username']) && !(@$_GET['view']==="public")) {
		$headline = 'Dein Profil';
		$link = '<a href="./profile?view=public">Wie sehen andere Nutzer mein Profil?</a><br>';
		$shEmail = ($thisuser['email']!="");
		$shRegDate = ($thisuser['regDate']!="");
		$shAvatar = ($thisuser['avatar']!="");
		$shHobbys = ($thisuser['hobbys']!="");
		$shFreitext = ($thisuser['description']!="");
		$shVorname = ($thisuser['firstname']!="");
		$shNachname = ($thisuser['lastname']!="");
		$shGeschlecht = ($thisuser['gender']!="");
		$shStrasse = ($thisuser['street']!="");
		$shHausnummer = ($thisuser['housenumber']!="");
		$shPlzOrt = ($thisuser['postalcode']!="");
		$shTelefon = ($thisuser['telefonnumber']!="");
		$shMessenger = ($thisuser['messengernumber']!="");
		// ALEX2: Hides day and month of birth if one value is equal 0 or empty.
		$shGeburtstag = ((substr($thisuser['birthday'],8,2) != 0) && (substr($thisuser['birthday'],8,2) != "")
			&& (substr($thisuser['birthday'],5,2) != 0) && (substr($thisuser['birthday'],5,2) != ""));
		// ALEX2: Hides year of birth in value is equal 0 or empty.
		$shJahrgang = ((substr($thisuser['birthday'],0,4) != "") && (substr($thisuser['birthday'],0,4) != 0));
	} else {
		$headline = "Profil von " . $thisuser['username'];
		$link = '';
		$shEmail = (substr($thisuser['privacykey'],0,1) === "1" && $thisuser['email']!="");
		$shRegDate = (substr($thisuser['privacykey'],1,1) === "1" && $thisuser['regDate']!="");
		$shAvatar = (substr($thisuser['privacykey'],2,1) === "1" && $thisuser['avatar']!="");
		$shHobbys = (substr($thisuser['privacykey'],3,1) === "1" && $thisuser['hobbys']!="");
		$shFreitext = (substr($thisuser['privacykey'],4,1) === "1" && $thisuser['description']!="");
		$shVorname = (substr($thisuser['privacykey'],5,1) === "1" && $thisuser['firstname']!="");
		$shNachname = (substr($thisuser['privacykey'],6,1) === "1" && $thisuser['lastname']!="");
		$shGeschlecht = (substr($thisuser['privacykey'],7,1) === "1" && $thisuser['gender']!="");
		$shStrasse = (substr($thisuser['privacykey'],8,1) === "1" && $thisuser['street']!="");
		$shHausnummer = (substr($thisuser['privacykey'],9,1) === "1" && $thisuser['housenumber']!="");
		$shPlzOrt = (substr($thisuser['privacykey'],10,1) === "1" && $thisuser['postalcode']!="0");
		$shTelefon = (substr($thisuser['privacykey'],11,1) === "1" && $thisuser['telefonnumber']!="");
		$shMessenger = (substr($thisuser['privacykey'],12,1) === "1" && $thisuser['messengernumber']!="");
		$shGeburtstag = (substr($thisuser['privacykey'],13,1) === "1" 
			&& ((substr($thisuser['birthday'],8,2) != 0) && (substr($thisuser['birthday'],8,2) != "")
			&& (substr($thisuser['birthday'],5,2) != 0) && (substr($thisuser['birthday'],5,2) != "")));
		$shJahrgang = (substr($thisuser['privacykey'],14,1) === "1" 
			&& ((substr($thisuser['birthday'],0,4) != "") && (substr($thisuser['birthday'],0,4) != 0)));
	}

	// ALEX2: Renamed $error in $errorMessage. 
	$errorMessage = "";
	// Fehlerüberprüfung, wenn das Formular zum speichern gesendet wurde.
	if(isset($_POST['action']) && ($_POST['action'] == 'save'))
	{			
		// ALEX2: Valid states: All empty, year or month/day filled, year/month/day filled.
		// Only day OR month filled?
		if((($_POST['txtDayOfBirth'] != "") && ($_POST['txtMonthOfBirth'] == "")) 
			|| (($_POST['txtDayOfBirth'] == "") && ($_POST['txtMonthOfBirth'] != "")))
		{
			$errorMessage = '<p>Bitte entweder Geburtsmonat und Geburtstag eintragen oder beides leer lassen.</p>';
		}
		// If day AND month filled, check values.
		else if(($_POST['txtDayOfBirth'] != "") && ($_POST['txtMonthOfBirth'] != ""))
		{
			if(!checkNumberAndRange($_POST['txtDayOfBirth'], 1, 31))
			{
				$errorMessage .= '<p>Geburtstag ist ungueltig.</p>';
			}
			if(!checkNumberAndRange($_POST['txtMonthOfBirth'], 1, 12))
			{
				$errorMessage .= '<p>Geburtsmonat ist ungueltig.</p>';
			}
		}
		// If filled, check year.
		if(($_POST['txtYearOfBirth'] != "") && !checkNumberAndRange($_POST['txtYearOfBirth'], 1900, 2000))
		{
			$errorMessage .= '<p>Geburtsjahr ist ungueltig.</p>';
		}
		
		// Check house number if street is set.
		// ALEX2: Advanced check for street and house number.
		// Scenarios: Both empty, both set and valid.
		if(($_POST['txtStrasse'] != "") || ($_POST['txtHausnummer'] != ""))
		{
			if((($_POST['txtStrasse'] != "") && ($_POST['txtHausnummer'] == ""))
				|| (($_POST['txtStrasse'] == "") && ($_POST['txtHausnummer'] != "")))
			{
				$errorMessage .= 'Bitte Strasse und Hausnummer zusammen angeben oder keins von beidem.';
			}
			/*
			else if (!is_numeric($_POST['txtHausnummer']))
			{
				$errorMessage .= 'Hausnummer ist ungueltig.';
			}
			*/
		}
		
		echo $errorMessage;
	}
			
	//Prüfen, ob das Eingabefeld angefordert wurde oder ob ein ungueltiger Eintrag gesetzt wurde.
	if((isset($_POST['action']) && ($_POST['action'] == 'edit')) || ($errorMessage != "")) {
		//Zeige die Seite mit Eingabefeldern zum Bearbeiten der Daten an

		$form_head = '<form action="" method="post" enctype="multipart/form-data">';
		//Block 0: Avatar
		$blAvatar = 'Pfad zum neuen Profilbild eingeben:<br>';
		$blAvatar .= '<input type="file" name="neuerAvatar" accept="image/*">';

		$blPersoenlich = "";
		$blPersoenlich .= '<h3>Persönliche Daten</h3><table style="border:none">';
		
		//Namen anzeigen:
		$blPersoenlich .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Realer Name:</td><td style="border:none">';
		$blPersoenlich .= $thisuser['firstname'] . ' ';
		$blPersoenlich .= $thisuser['lastname'];
		$blPersoenlich .= '</td></tr>';

		//Geschlecht bearbeiten:
		$blPersoenlich .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Geschlecht:</td><td style="border:none"><select name="txtGender" size=1><option' . (($thisuser['gender']==='w')?'':' selected') . '>Männlich</option><option' . (($thisuser['gender']==='w')?' select':'') . '>Weiblich</option></td></tr>';

		// ALEX2: Leaves text boxes empty if value is 0. Added $tempValue.
		$tempValue = getDayOfBirth($thisuser['birthday']);
		$tempValue = ($tempValue == 0) ? "" : $tempValue;
		// Text box for day of birth.
		$blPersoenlich .= '<tr><td>Geboren:</td><td><input type="text" size="2px"
		name="txtDayOfBirth" placeholder="TT" value="' . $tempValue . '">';	
		// Text box for month of birth.
		$tempValue = getMonthOfBirth($thisuser['birthday']);
		$tempValue = ($tempValue == 0) ? "" : $tempValue;
		$blPersoenlich .= '.<input type="text" size="2px" name="txtMonthOfBirth" placeholder="MM" value="' . $tempValue . '">';	
		// Text box for year of birth.
		$tempValue = getYearOfBirth($thisuser['birthday']);
		$tempValue = ($tempValue == 0) ? "" : $tempValue;
		$blPersoenlich .= '.<input type="text" size="4px" name="txtYearOfBirth" placeholder="JJJJ" value="' . $tempValue . '"></td></tr>';		

		//RegDate anzeigen
		$blPersoenlich .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Tut Gutes seit:</td><td style="border:none">' . substr($thisuser['regDate'],8,2) . '. ' . substr($thisuser['regDate'],5,2) . '. ' . substr($thisuser['regDate'],0,4) . '</td></tr>';
		$blPersoenlich .= "</table>";

		//RegDate anzeigen
		$blPersoenlich .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Tut Gutes seit:</td><td style="border:none">' . substr($thisuser['regDate'],8,2) . '. ' . substr($thisuser['regDate'],5,2) . '. ' . substr($thisuser['regDate'],0,4) . '</td></tr>';
		$blPersoenlich .= "</table>";

		//Block 2: Über USER
		$blUeber = "";
		$blUeber .= '<h3>Über ' . $thisuser['username'] . '</h3><table style="border:none;width:45%">';
		
		//Hobbys anzeigen:
		$blUeber .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Hobbys:</td><td style="border:none"><input type="text" name="txtHobbys" placeholder="z.B. Kochen, Fahrrad fahren, ..." value="' . $thisuser['hobbys'] . '" style="width:100%"></td></tr>';

		//Freitext anzeigen:
		$blUeber .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Über mich:</td><td style="border:none"><textarea rows=20 cols=100 placeholder="beschreibe dich selbst mit wenigen Worten..." name="txtBeschreibung">' . $thisuser['description'] . '</textarea></td></tr>';

		$blUeber .= "</table>";

		//Block 3: Taten
		$blTaten = '<h3>Taten von ' . $thisuser['username'] . '</h3><table style="border:none">';
		$blTaten .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Karma:</td><td style="border:none">';
		$blTaten .= $thisuser['points'] . ' (' . $thisuser['trustleveldescription'] . ')';

		$blTaten .= "</table>";

		//Block 4: Adresse 
		$blAdresse = "";		
		$blAdresse .= '<h3>Wo findet man ' . $thisuser['username'] . '?</h3><table style="border:none">';
		$showMap = false;
		
		//Adresse eingeben:
		$blAdresse .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Adresse:</td><td style="border:none">';

		// ALEX: Strassenliste einfügen.
		$blAdresse .= '<input type="search" list="lstStreets" name="txtStrasse" onchange="updatePLZPlace();" placeholder="Strasse" value="' . $thisuser['street'] . '"><br>';
		$blAdresse .= $streetList;
		$blAdresse .= '</td>';

		/*$blAdresse .= '<input type="text" name="txtStrasse" placeholder="Strasse" value="' . $thisuser['street'] . '">';*/
		
		// ALEX: Hausnummer einfügen.
		$blAdresse .= '<td style="border:none"><input type="text" size="5%" name="txtHausnummer" placeholder="Nr." value="' 
			. $thisuser['housenumber'] . '">';
		$blAdresse .= "</td></tr>";
		$blAdresse .= '<tr><td></td><td colspan=2><input type="submit" value="meine Adresse fehlt..." form="suggestAddress" /></td></tr>';

		// ALEX: Auskommentiert.
		//PLZ/Ort bearbeiten:
		//TODO: Postleitzahl überprüfen
		/*$blAdresse .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">PLZ:</td><td style="border:none"><input type="text" name="txtPostleitzahl" placeholder="PLZ" value="' . (($thisuser['postalcode']!=0)?$thisuser['postalcode']:'') . '"></td></tr>';
		$blAdresse .= "</table>";*/

		//PLZ/Ort bearbeiten:
//		$blAdresse .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">PLZ:</td><td style="border:none"><input type="text" name="txtPostleitzahl" placeholder="PLZ" value="' . (($thisuser['postalcode']!=0)?$thisuser['postalcode']:'') . '"></td>';
//		$blAdresse .= '<td style="border:none"><input type="text" name="txtOrt" placeholder="Ort" value="' . (($thisuser['postalcode']!=0)?$thisuser['place']:'') . '"></td></tr>';
		$blAdresse .= "</table>";

		//Block 5: Kontakt
		$blKontakt = '<h3>Kontakt</h3><table style="border:none">';
		
		//Email anzeigen:
		$blKontakt .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Email:</td><td style="border:none"><a href="mailto:' . $thisuser['email'] . '">' . $thisuser['email'] . '</a></td></tr>';
		
		//Telefonnummer bearbeiten:
		$blKontakt .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Telefon:</td><td style="border:none"> <input type="text" name="txtTelNr" placeholder="z.B. 051112345678" value="' . $thisuser['telefonnumber'] . '"></td></tr>';
		
		//Messengernummer bearbeiten:
		$blKontakt .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Messenger:</td><td style="border:none"><input type="text" name="txtMsgNr" value="' . $thisuser['messengernumber'] . '"></td></tr>';

		$blKontakt .= "</table>";

		//Block 6: Sichtbarkeit
		$blPrivacy = '<h3>Sichtbarkeitseinstellungen</h3>Welche Einstellungen sollen Besuchern deines Profils angezeigt werden?<br><br><table>';
		$blPrivacy .= '<tr><td><select name="vsMail"/><option value="1">Anzeigen</option><option value="0" ' . (substr($thisuser['privacykey'],0,1)?'':'selected') . '>Verbergen</option></select> Meine E-Mail Adresse</td></tr>';
		$blPrivacy .= '<tr><td><select name="vsRegDate"/><option value="1">Anzeigen</option><option value="0" ' . (substr($thisuser['privacykey'],1,1)?'':'selected') . '>Verbergen</option></select> Datum meiner Registrierung</td></tr>';
		$blPrivacy .= '<tr><td><select name="vsAvatar"/><option value="1">Anzeigen</option><option value="0" ' . (substr($thisuser['privacykey'],2,1)?'':'selected') . '>Verbergen</option></select> Mein Profilbild</td></tr>';
		$blPrivacy .= '<tr><td><select name="vsHobbys"/><option value="1">Anzeigen</option><option value="0" ' . (substr($thisuser['privacykey'],3,1)?'':'selected') . '>Verbergen</option></select> Meine Hobbys</td></tr>';
		$blPrivacy .= '<tr><td><select name="vsDescription"/><option value="1">Anzeigen</option><option value="0" ' . (substr($thisuser['privacykey'],4,1)?'':'selected') . '>Verbergen</option></select> Meine Beschreibung von mir selbst</td></tr>';
		$blPrivacy .= '<tr><td><select name="vsFirstname"/><option value="1">Anzeigen</option><option value="0" ' . (substr($thisuser['privacykey'],5,1)?'':'selected') . '>Verbergen</option></select> Meinen Vornamen</td></tr>';
		$blPrivacy .= '<tr><td><select name="vsLastname"/><option value="1">Anzeigen</option><option value="0" ' . (substr($thisuser['privacykey'],6,1)?'':'selected') . '>Verbergen</option></select> Meinen Nachnamen</td></tr>';
		$blPrivacy .= '<tr><td><select name="vsGender"/><option value="1">Anzeigen</option><option value="0" ' . (substr($thisuser['privacykey'],7,1)?'':'selected') . '>Verbergen</option></select> Mein Geschlecht</td></tr>';
		$blPrivacy .= '<tr><td><select name="vsStreet"/><option value="1">Anzeigen</option><option value="0" ' . (substr($thisuser['privacykey'],8,1)?'':'selected') . '>Verbergen</option></select> Meine Straße</td></tr>';
		$blPrivacy .= '<tr><td><select name="vsHousenumber"/><option value="1">Anzeigen</option><option value="0" ' . (substr($thisuser['privacykey'],9,1)?'':'selected') . '>Verbergen</option></select> Meine Hausnummer</td></tr>';
		$blPrivacy .= '<tr><td><select name="vsPlzOrt"/><option value="1">Anzeigen</option><option value="0" ' . (substr($thisuser['privacykey'],10,1)?'':'selected') . '>Verbergen</option></select> Meinen Wohnort</td></tr>';
		$blPrivacy .= '<tr><td><select name="vsTelNr"/><option value="1">Anzeigen</option><option value="0" ' . (substr($thisuser['privacykey'],11,1)?'':'selected') . '>Verbergen</option></select> Meine Telefonnummer</td></tr>';
		$blPrivacy .= '<tr><td><select name="vsMsgNr"/><option value="1">Anzeigen</option><option value="0" ' . (substr($thisuser['privacykey'],12,1)?'':'selected') . '>Verbergen</option></select> Meine Messengernummer</td></tr>';
		$blPrivacy .= '<tr><td><select name="vsBirthday"/><option value="1">Anzeigen</option><option value="0" ' . (substr($thisuser['privacykey'],13,1)?'':'selected') . '>Verbergen</option></select> Meinen Geburtstag</td></tr>';
		$blPrivacy .= '<tr><td><select name="vsBirthyear"/><option value="1">Anzeigen</option><option value="0" ' . (substr($thisuser['privacykey'],14,1)?'':'selected') . '>Verbergen</option></select> Mein Geburtsjahr</td></tr>';
		$blPrivacy .= '</table>';

		$form_bottom = '<p /><p /><input type=submit value="Änderungen speichern"><input type="hidden" name="action" value="save"><br><br><br><br><br></form>';
		// ALEX2: Increased text box size.
		$form_bottom .= '<form action="" method="post"><input type="password" size="50px" name="save_pw" placeholder="Passwort zur Sicherheit erneut eingeben..."><br><br><input type=submit value="Profil entgültig löschen"></form>';

	} else {
		//Zeige das Profil ohne Änderungsmöglichkeit an

		//Ggf. Speichern / Verwerfen der geänderten Werte
		if(isset($_POST['action']) && ($_POST['action'] == 'save')) 
		{
			// ALEX: If street is set, check name and find postal code. 
			if($_POST['txtStrasse'] !== '')
			{				
				// Find postal code and place to address.
				$lFoundValues = getPostalPlaceToAddress($_POST['txtStrasse'], $_POST['txtHausnummer']);
				
				// ALEX2: Added check for valid house number.
				$searchString = ">" . $_POST['txtStrasse'] . "<";
				
				// If no valid street or house number was entered, send mail to moderator.
				if ((stripos($streetList, $searchString) === false) || ($lFoundValues['retHouseNumber'] == ""))
				{
					// ALEX2
					sendEmail('alexander.gauggel@stud.hs-hannover.de', 'TG_Strasse', 'Bitte Strasseneingabe "' 
						. $_POST['txtStrasse'] . '" von User "' . $thisuser['username'] . '" pruefen.');
				}
				
				if(is_numeric($lFoundValues['retPostal']))
				{
					// If postal code wasn't found in DB, add it and set foreign key in userdata.
					$lIdPostal = DBFunctions::db_getIdPostalbyPostalcodePlace($lFoundValues['retPostal'], $lFoundValues['retPlace']);
					
					// If no corresponding postal code was found, add it to database.
					if($lIdPostal == "")
					{			
						DBFunctions::db_insertPostalCode($lFoundValues['retPostal'], $lFoundValues['retPlace']);
						$IdPostal = DBFunctions::db_getIdPostalbyPostalcodePlace($lFoundValues['retPostal'], $lFoundValues['retPlace']);
					}
					
					$thisuser['idPostal'] = $lIdPostal;
					
				}
			}

			// Nutzerdaten überschreiben:
			
			// ALEX2: Replace empty values with 0. Created $tempValue.
			$tempValue = ($_POST['txtYearOfBirth'] == "") ? 0 : $_POST['txtYearOfBirth'];
			$thisuser['birthday'] = $tempValue . '-';
			$tempValue = ($_POST['txtMonthOfBirth'] == "") ? 0 : $_POST['txtMonthOfBirth'];
			$thisuser['birthday'] .= $tempValue . '-';
			$tempValue = ($_POST['txtDayOfBirth'] == "") ? 0 : $_POST['txtDayOfBirth'];
			$thisuser['birthday'] .= $tempValue;

			//Speichern des Profilbildes
			if ($_FILES['neuerAvatar']['name'] != '') {
				$uploadDir = './img/profiles/'.$thisuser['idUser'].'/';
				if (!is_dir($uploadDir)) mkdir($uploadDir);
				imagepng(imagecreatefromstring(file_get_contents($_FILES['neuerAvatar']['tmp_name'])), $uploadDir . 'converted.png');
				$size = getimagesize($uploadDir . 'converted.png');
				
				//Anlegen der Dateien
				$uploaded = imagecreatefrompng($uploadDir . 'converted.png');
				$avatar_512 = imagecreatetruecolor(512,512);
				$avatar_256 = imagecreatetruecolor(256,256);
				$avatar_128 = imagecreatetruecolor(128,128);
				$avatar_64 = imagecreatetruecolor(64,64);
				$avatar_32 = imagecreatetruecolor(32,32);

				//Resizing
				imagecopyresized($avatar_512, $uploaded, 0, 0, 0, 0, 512, 512 , $size[0], $size[1]);
				imagecopyresized($avatar_256, $uploaded, 0, 0, 0, 0, 256, 256 , $size[0], $size[1]);
				imagecopyresized($avatar_128, $uploaded, 0, 0, 0, 0, 128, 128 , $size[0], $size[1]);
				imagecopyresized($avatar_64, $uploaded, 0, 0, 0, 0, 64, 64 , $size[0], $size[1]);
				imagecopyresized($avatar_32, $uploaded, 0, 0, 0, 0, 32, 32 , $size[0], $size[1]);

				imagepng($avatar_512, $uploadDir . '512x512.png');
				imagepng($avatar_256, $uploadDir . '256x256.png');
				imagepng($avatar_128, $uploadDir . '128x128.png');
				imagepng($avatar_64, $uploadDir . '64x64.png');
				imagepng($avatar_32, $uploadDir . '32x32.png');

				unlink($uploadDir . 'converted.png');

				//Speichern des neuen Avatars im Nutzerprofil
				$thisuser['avatar'] = $uploadDir.'512x512.png';
			}
			$thisuser['street'] = $_POST['txtStrasse'];
			$thisuser['housenumber'] = $_POST['txtHausnummer'];
			$thisuser['messengernumber'] = $_POST['txtMsgNr'];
			$thisuser['telefonnumber'] = $_POST['txtTelNr'];
			$thisuser['hobbys'] = $_POST['txtHobbys'];
			$thisuser['description'] = $_POST['txtBeschreibung'];
			$thisuser['postalcode'] = getPostalPlaceToAddress($thisuser['street'], $thisuser['housenumber'])['retPostal'];
			$thisuser['place'] = getPostalPlaceToAddress($thisuser['street'], $thisuser['housenumber'])['retPlace'];
			$thisuser['privacykey'] = $_POST['vsMail'] . $_POST['vsRegDate'] . $_POST['vsAvatar'] . $_POST['vsHobbys'] . $_POST['vsDescription'] . $_POST['vsFirstname'] . $_POST['vsLastname'] . $_POST['vsGender'] . $_POST['vsStreet'] . $_POST['vsHousenumber'] . $_POST['vsPlzOrt'] . $_POST['vsTelNr'] . $_POST['vsMsgNr'] . $_POST['vsBirthday'] . $_POST['vsBirthyear'];

			//Änderungen speichern
			DBFunctions::db_update_user($thisuser);
			header("Refresh:0");
		}

		//Anzeige des eigentlichen Nutzerprofils

		$form_head = '<form action="" method="post">';

		//Block 0: Avatar
		$blAvatar = '';
		if ($shAvatar) $blAvatar .= '<div align=center><img id="avatar" src="' . $thisuser['avatar'] . '" width="150px"></div>';

		//Block 1: Persönliches
		$blPersoenlich = "";
		if ($shVorname || $shNachname || $shGeburtstag || $shJahrgang || $shRegDate) {

			$blPersoenlich .= '<h3>Persönliche Daten</h3><table style="border:none">';

			//Namen anzeigen:
			if ($shVorname || $shNachname) {
				$blPersoenlich .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Realer Name:</td><td style="border:none">';
				if ($shVorname) $blPersoenlich .= $thisuser['firstname'] . ' ';
				if ($shNachname) $blPersoenlich .= $thisuser['lastname'];
				$blPersoenlich .= "</td></tr>";
			}

			//Geschlecht anzeigen:
			if ($shGeschlecht) $blPersoenlich .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Geschlecht:</td><td style="border:none">' . (($thisuser['gender']==='w')?'weiblich':'männlich') . '</td></tr>';

			//Geburtstag anzeigen:
			if ($shGeburtstag || $shJahrgang) {
				$blPersoenlich .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Geboren:</td><td style="border:none">';
				if ($shGeburtstag) $blPersoenlich .= substr($thisuser['birthday'],8,2) . '. ' . substr($thisuser['birthday'],5,2) . '. ';
				if ($shJahrgang) $blPersoenlich .= substr($thisuser['birthday'],0,4);
				$blPersoenlich .= "</td></tr>";
			}

			//RegDate anzeigen
			if ($shRegDate) $blPersoenlich .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Tut Gutes seit:</td><td style="border:none">' . substr($thisuser['regDate'],8,2) . '. ' . substr($thisuser['regDate'],5,2) . '. ' . substr($thisuser['regDate'],0,4) . '</td></tr>';

			$blPersoenlich .= "</table>";
		}

		//Block 2: Über User
		$blUeber = "";
		if ($shHobbys || $shFreitext) {
			$blUeber .= '<h3>Über ' . $thisuser['username'] . '</h3><table style="border:none;width:45%">';
			
			//Hobbys anzeigen:
			if ($shHobbys) $blUeber .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Hobbys:</td><td style="border:none">' . $thisuser['hobbys'] . '</td></tr>';

			//Freitext anzeigen:
			if ($shFreitext) $blUeber .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Über mich:</td><td style="border:none">' . $thisuser['description'] . '</td></tr>';

			$blUeber .= "</table>";
		}	

		//Block 3: Taten
		$blTaten = '<h3>Taten von ' . $thisuser['username'] . '</h3><table style="border:none">';
		$blTaten .= 'Karma: ' . $thisuser['points'] . ' (' . $thisuser['trustleveldescription'] . ')';


		//Die letzten Taten des Nutzers
		$arr = DBFunctions::db_getGuteTatenForUser(0,3,'alle',$thisuser['idUser']);
		if (sizeof($arr)==0) {
			$blTaten .= '<br>' . $thisuser['username'] . ' hat noch keine guten Taten...' . ((($thisuser['username']==$_USER->getUsername()) && !(@$_GET['view']!='public'))?'<br><a href="./deeds">Jetzt gute Taten finden</a>':'');
		} else {
			$maxZeichenFürDieKurzbeschreibung = 150;
			$blTaten .= '<br><br><h5>Aktuelle Taten:</h5>';
			for($i = 0; $i < sizeof($arr); $i++){
					$blTaten .= '<br>';
					$blTaten .=  "<a href='./deeds_details?id=" . $arr[$i]->idGuteTat . "' class='deedAnchor'><div class='deed" . ($arr[$i]->status == "geschlossen" ? " closed" : "") . "'>";
					$blTaten .=  "<div class='name'><h4>" . $arr[$i]->name . "</h4></div><div class='category'>" . $arr[$i]->category . "</div>";
					$blTaten .=  "<br><br><br><br><div class='description'>" . (strlen($arr[$i]->description) > $maxZeichenFürDieKurzbeschreibung ? substr($arr[$i]->description, 0, $maxZeichenFürDieKurzbeschreibung) . "...<br>mehr" : $arr[$i]->description) . "</div>";
					$blTaten .=  "<div class='address'>" . $arr[$i]->street .  "  " . $arr[$i]->housenumber . "<br>" . $arr[$i]->postalcode . ' / ' . $arr[$i]->place . "</div>";
					$blTaten .=  "<div>" . (is_numeric($arr[$i]->countHelper) ? "Anzahl der Helfer: " . $arr[$i]->countHelper : '') ."</div><div class='trustLevel'>Minimaler Vertrauenslevel: " . $arr[$i]->idTrust . " (" . $arr[$i]->trustleveldescription . ")</div>";
					$blTaten .=  "<div>" . $arr[$i]->organization . "</div>";
					$blTaten .=  "</div></a>";
					$blTaten .=  "<br>";
				}
			$blTaten .= ((($thisuser['username']==$_USER->getUsername()) && !(@$_GET['view']!='public'))?'<br><a href="./deeds?user=' . $thisuser['username'] . '">Alle deine guten Taten</a>':'<br><a href="./deeds?user=' . $thisuser['username'] . '">Alle guten Taten des Nutzers</a>');
		}


		//Block 4: Adresse 
		$blAdresse = "";
		if ($shStrasse || $shHausnummer || $shPlzOrt) {
			
			$blAdresse .= '<h3>Wo findet man ' . $thisuser['username'] . '?</h3><table style="border:none">';
			//Adresse anzeigen:
			// ALEX: Strasse und Hausnummer werden nur angezeigt, wenn entweder mind. die Straße oder beides gesetzt ist.
			if ($shStrasse) 
			{
				$blAdresse .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Adresse:</td><td style="border:none">';
				$blAdresse .= $thisuser['street'];
				if ($shHausnummer) 
				{
					$blAdresse .= ' ' . $thisuser['housenumber'];
				}
				$blAdresse .= "</td></tr>";
			}

			//PLZ/Ort anzeigen:
			if ($shPlzOrt) $blAdresse .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">PLZ/Ort:</td><td style="border:none">' . $thisuser['postalcode'] . ' / ' . $thisuser['place'] . '</td></tr>';

			$blAdresse .= '</table>';
		}
		$showMap = ($shPlzOrt && $shStrasse && $shHausnummer);

		//Block 5: Kontakt
		$blKontakt = "";
		if ($shEmail || $shTelefon || $shMessenger) {
			
			$blKontakt .= '<h3>Kontakt</h3><table style="border:none">';
			
			//Email anzeigen:
			if ($shEmail) $blKontakt .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Email:</td><td style="border:none"><a href="mailto:' . $thisuser['email'] . '">' . $thisuser['email'] . '</a></td></tr>';
			
			//Telefonnummer anzeigen:
			if ($shTelefon) $blKontakt .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Telefon:</td><td style="border:none">' . $thisuser['telefonnumber'] . '</td></tr>';
			
			//Messengernummer anzeigen:
			if ($shMessenger) $blKontakt .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Messenger:</td><td style="border:none">' . $thisuser['messengernumber'] . '</td></tr>';

			$blKontakt .= "</table>";

		}

		$blPrivacy = '';

		$form_bottom = (((strtoupper($_USER->getUsername())===strtoupper($thisuser['username'])) && (!isset($_GET['view']) || $_GET['view'] != "public") )?'<p /><p /><input type="submit" value="Profil bearbeiten"><input type="hidden" name="action" value="edit"></form>':'</form>');
	}

?>

<style>
	#mapid {
		height:350px;
		width:350px;
	}
</style>

<!--Überschrift-->
<h2 id="profileheader"><?php echo $headline; ?></h2>

<!--Ggf. ausgeben des Links zur öffentlichen Ansicht-->
<?php echo $link; ?>

<!--Beginn des Formulars zum Ändern der Profileinstellungen:-->
<?php echo $form_head; ?>

<!--Ausgabe der einzelnen Blöcke-->
<?php 
	echo $blAvatar;
	echo '<p />';
	echo '<div align="center">' . $blPersoenlich . '</div>';
	echo '<p />';
	echo '<div align="center">' . $blUeber . '</div>';
	echo '<p />';
	echo '<div align="center">' . $blTaten . '</div>';
	echo '<p />';
	echo '<div align="center">' . $blAdresse . '</div>';
	echo '<p />';
	echo '<div align="center">' . $blKontakt . '</div>';
	
	if ($showMap) {
		echo '<center><div id="mapid"></div>';
		$map = new Map();
		$map->createMap($thisuser['postalcode'] . ',' . $thisuser['street'] . ',' . $thisuser['housenumber']);
		echo '</center>';
	}
	
	echo '<p />';
	echo '<div align="center">' . $blPrivacy . '</div>';
	//echo '<p />';
	//echo '<div align="center">' . $blPrivacy . '</div>';
?>

<!--Ende des Formulars zum Ändern der Profileinstellungen:-->
<?php
echo $form_bottom; ?>

<?php require "./includes/_bottom.php"; ?>
