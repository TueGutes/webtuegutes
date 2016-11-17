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
		// Defining map to return.
		$lRetVals = [
			"retPostal" => "",
			"retPlace" => "",
		];
		if(!is_numeric($pHouseNumber))
		{
			$pHouseNumber = 1;
		}
		
		// Create address in format "<street>[+<street appendices],<house number>,Hannover".
		$lAddressString = $pStreet . ',' . $pHouseNumber . ',Hannover';
		// Replace empty spaces.
		$lAddressString = str_replace(' ', '+', $lAddressString);
		// Get JSON result.
		$lContents = file('http://nominatim.openstreetmap.org/search?format=json&limit=2&q=' . $lAddressString);
		// Put string in new variable for safety.
		$lResult = $lContents[0];
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
		
		return $lRetVals;
	}

	//Includes
	require "./includes/DEF.php";
	
	include "./includes/db_connector.php";
	include "./includes/Map.php";
	require "./includes/_top.php";

	// ALEX
	include "./includes/streets.php";

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
		$shGeburtstag = ($thisuser['birthday']!="");
		$shJahrgang = ($thisuser['birthday']!="");
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
		$shGeburtstag = (substr($thisuser['privacykey'],13,1) === "1" && $thisuser['birthday']!="");
		$shJahrgang = (substr($thisuser['privacykey'],14,1) === "1" && $thisuser['birthday']!="");
	}

	// ALEX
	// Variable, die angibt, ob ungültige Daten eingegeben wurden.
	$error = false;	
	// Fehlerüberprüfung, wenn das Formular zum speichern gesendet wurde.
	if(isset($_POST['action']) && ($_POST['action'] == 'save'))
	{			
		// Geburtsdaten überprüfen. (Nur einfache Prüfung auf 01 <= Tag <= 31 und 01 <= Monat <= 12 nd 1900 <= Jahr <= 2016
		if((!is_numeric($_POST['txtDayOfBirth'])) && ($_POST['txtDayOfBirth'] < 1) || ($_POST['txtDayOfBirth'] > 31))
		{
			echo 'Tag im Geburtsdatum ist ungueltig.';
			$error = true;
		}
		if((!is_numeric($_POST['txtMonthOfBirth'])) && ($_POST['txtMonthOfBirth'] < 1) || ($_POST['txtMonthOfBirth'] > 12))
		{
			echo 'Monat im Geburtsdatum ist ungueltig.';
			$error = true;
		}
		if((!is_numeric($_POST['txtYearOfBirth'])) && ($_POST['txtYearOfBirth'] < 1900) || ($_POST['txtYearOfBirth'] > 2016))
		{
			echo 'Jahr im Geburtsdatum ist ungueltig.';
			$error = true;
		}
		if(($_POST['txtHausnummer'] != "") && (!is_numeric($_POST['txtHausnummer'])))
		{
			echo 'Hausnummer ist ungültig.';
			$error = true;
		}
	}
			
	//Prüfen, ob das Eingabefeld angefordert wurde oder ob ein ungueltiger Eintrag gesetzt wurde.
	if((isset($_POST['action']) && ($_POST['action'] == 'edit')) || ($error === true)) {
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

		// Textbox für Tag.
		$blPersoenlich .= '<tr><td>Geboren:</td><td><input type="text" size="2px"
		name="txtDayOfBirth" placeholder="TT" value="' . getDayOfBirth($thisuser['birthday']) . '">';	

		// Textbox für Monat.
		$blPersoenlich .= '.<input type="text" size="2px" name="txtMonthOfBirth" placeholder="MM" value="' . getMonthOfBirth($thisuser['birthday']) . '">';		

		// Textbox für Jahr.
		$blPersoenlich .= '.<input type="text" size="4px" name="txtYearOfBirth" placeholder="JJJJ" value="' . getYearOfBirth($thisuser['birthday']) . '"></td></tr>';		

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
			//Hier können noch weitere Informationen wie z.B. die letzten Guten Taten des Nutzers, die von ihm ausgeschriebenen Taten, etc. aufgeführt werden
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
		$form_bottom .= '<form action="" method="post"><input type="password" name="save_pw" placeholder="Passwort zur Sicherheit erneut eingeben..."><br><br><input type=submit value="Profil entgültig löschen"></form>';

	} else {
		//Zeige das Profil ohne Änderungsmöglichkeit an

		//Ggf. Speichern / Verwerfen der geänderten Werte
		if(isset($_POST['action']) && ($_POST['action'] == 'save')) 
		{
			// ALEX: If street is set, check name and find postal code. 
			if($_POST['txtStrasse'] !== '')
			{
				// Check if street list contains entered street. If not, send mail to moderator.
				$searchString = ">" . $_POST['txtStrasse'] . "<";
				if (stripos($streetList, $searchString) === false) 
				{
					// TODO: Mail senden.
				}
				// Find postal code and place to address.
				$lFoundValues = getPostalPlaceToAddress($_POST['txtStrasse'], $_POST['txtHausnummer']);
								
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
			$thisuser['birthday'] = $_POST['txtYearOfBirth'] . '-' . $_POST['txtMonthOfBirth'] . '-' . $_POST['txtDayOfBirth'];
			if ($_FILES['neuerAvatar']['name'] != '') {
				move_uploaded_file($_FILES['neuerAvatar']['tmp_name'],'./img/tmp/avatar_' . $thisuser['idUser']);
				$thisuser['avatar'] = 'data: ' . mime_content_type('./img/tmp/avatar_' . $thisuser['idUser']) . ';base64,' . base64_encode(file_get_contents('./img/tmp/avatar_' . $thisuser['idUser']));
				unlink('./img/tmp/avatar_' . $thisuser['idUser']); //Später kann diese Zeile ggf. gelöscht werden
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
		$blTaten .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Karma:</td><td style="border:none">';
		$blTaten .= $thisuser['points'] . ' (' . $thisuser['trustleveldescription'] . ')';
			//Hier können noch weitere Informationen wie z.B. die letzten Guten Taten des Nutzers, die von ihm ausgeschriebenen Taten, etc. aufgeführt werden
		$blTaten .= "</table>";

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
		createMap($thisuser['postalcode'] . ',' . $thisuser['street'] . ',' . $thisuser['housenumber']);
		echo '</center>';
	}
	
	echo '<p />';
	echo '<div align="center">' . $blPrivacy . '</div>';
	//echo '<p />';
	//echo '<div align="center">' . $blPrivacy . '</div>';
?>

<!--Ende des Formulars zum Ändern der Profileinstellungen:-->
<?php echo $form_bottom; ?>

<?php require "./includes/_bottom.php"; ?>

