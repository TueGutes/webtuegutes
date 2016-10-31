<?php
	//TEMPORÄRER CODEBLOCK! Bitte in db_connector auslagern und diesen Block dann löschen!

	function fix_plz($plz) {
		$db = db_connect();
		$sql = "SELECT * from POSTALCODE where Postalcode = ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('i',$plz);
		$stmt->execute();
		$result = $stmt->get_result();
		if (!isset($result->fetch_assoc['postalcode'])) {
			$sql = 'INSERT INTO POSTALCODE (postalcode, place) VALUES (?, "Unbekannt")';
			$stmt = $db->prepare($sql);
			$stmt->bind_param('i',$plz);
			$stmt->execute();
		}
		db_close($db);
	}

	function get_user($user) {
		$db = db_connect();
		$sql = "
			SELECT idUser, username, email, regDate, points, trustleveldescription, groupDescription, privacykey, avatar, hobbys, description, firstname, lastname, gender, street, housenumber, postalcode, telefonnumber, messengernumber, birthday 
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
				FROM postalcode
				WHERE postalcode = ?
			";
			$stmt = $db->prepare($sql);
			$stmt->bind_param('s',$thisuser['postalcode']);
			$stmt->execute();
			$thisuser['place'] = $stmt->get_result()->fetch_assoc()['place'];
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
	function update_user($savedata)
	{
		fix_plz($savedata['postalcode']);
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

	function upload_avatar($file, $uid) {
		$tmpfile = fopen('./img/tmp/avatar_' . $uid . '.png', 'w') or die("Unable to open file!");
		fwrite($tmpfile, file_get_contents($file));
		fclose($tmpfile);
	}

?>

<?php
/*
* @author: Nick Nolting, Alexander Gauggel
*/

	//Includes
	include "./db/db_connector.php";
	include "./includes/session.php";	
	require "./includes/_top.php";

	//Festlegen des auszulesenden Nutzers:
	$thisuser = get_user(@$_GET['user']);
	if (!isset($thisuser['username'])) $thisuser = get_user($_SESSION['user']);

	//Festlegen der Sichtbarkeitseinstellungen
	if ($_SESSION['user']===$thisuser['username'] && !(@$_GET['view']==="public")) {
		$headline = 'Dein Profil';
		$link = '<a href="profile.php?view=public">Wie sehen andere Nutzer mein Profil?</a><br>';
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
		$shPlzOrt = (substr($thisuser['privacykey'],10,1) === "1" && $thisuser['postalcode']!="");
		$shTelefon = (substr($thisuser['privacykey'],11,1) === "1" && $thisuser['telefonnumber']!="");
		$shMessenger = (substr($thisuser['privacykey'],12,1) === "1" && $thisuser['messengernumber']!="");
		$shGeburtstag = (substr($thisuser['privacykey'],13,1) === "1" && $thisuser['birthday']!="");
		$shJahrgang = (substr($thisuser['privacykey'],14,1) === "1" && $thisuser['birthday']!="");
	}

	//Prüfen, ob das Eingabefeld angefordert wurde
	if(isset($_POST['action']) && ($_POST['action'] == 'edit')) {
		//Zeige die Seite mit Eingabefeldern zum Bearbeiten der Daten an

		$form_head = '<form action="" method="post" enctype="multipart/form-data">';
		//Block 0: Avatar
		$blAvatar = 'Pfad zum neuen Profilbild eingeben:<br>';
		$blAvatar .= '<input type="file" name="neuerAvatar" accept="image/*">';
		//$blAvatar .= '<input type="text" name="txtAvatar" placeholder="Pfad zum neuen Avatar...">';

		$blPersoenlich = "";
		$blPersoenlich .= '<h3>Persönliche Daten</h3><table style="border:none">';
		
		//Namen anzeigen:
		$blPersoenlich .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Realer Name:</td><td style="border:none">';
		$blPersoenlich .= $thisuser['firstname'] . ' ';
		$blPersoenlich .= $thisuser['lastname'];
		$blPersoenlich .= '</td></tr>';

		//Geschlecht anzeigen:
		$blPersoenlich .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Geschlecht:</td><td style="border:none">' . (($thisuser['gender']==='w')?'weiblich':'männlich') . '</td></tr>';

		//Geburtstag bearbeiten:
		$blPersoenlich .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Geboren:</td><td style="border:none"><input type="text" name="txtGeburtstag" placeholder="JJJJ-MM-TT" value="' . $thisuser['birthday'] . '"></td></tr>';

		//RegDate anzeigen
		$blPersoenlich .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Tut Gutes seit:</td><td style="border:none">' . substr($thisuser['regDate'],8,2) . '. ' . substr($thisuser['regDate'],5,2) . '. ' . substr($thisuser['regDate'],0,4) . '</td></tr>';
		$blPersoenlich .= "</table>";

		//Block 2: Über USER
		$blUeber = "";
		$blUeber .= '<h3>Über ' . $thisuser['username'] . '</h3><table style="border:none;width:45%">';
		
		//Hobbys anzeigen:
		$blUeber .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Hobbys:</td><td style="border:none"><input type="text" name="txtHobbys" placeholder="z.B. Kochen, Fahrrad fahren, ..." value="' . $thisuser['hobbys'] . '"></td></tr>';

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
		
		//Adresse eingeben:
		$blAdresse .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Adresse:</td><td style="border:none">';
		$blAdresse .= '<input type="text" name="txtStrasse" placeholder="Strasse" value="' . $thisuser['street'] . '">';
		$blAdresse .= '<input type="text" size="5%" name="txtHausnummer" placeholder="Nr." value="' . $thisuser['housenumber'] . '">';
		$blAdresse .= "</td></tr>";

		//PLZ/Ort bearbeiten:
						//TODO: Postleitzahl überprüfen
		$blAdresse .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">PLZ:</td><td style="border:none"><input type="text" name="txtPostleitzahl" placeholder="PLZ" value="' . $thisuser['postalcode'] . '"></td></tr>';
		$blAdresse .= "</table>";

		//Kartenansicht
		if ($shStrasse && $shHausnummer && $shPlzOrt) {

			//HIER DEN CODE FÜR DIE KARTE EINBINDEN UND AN $blAdresse KONKATENIEREN!

			//Die folgende Zeile ist nur ein Platzhalter und kann anschließend gelöscht werden:
			$blAdresse .= '<div align=center><img id="map" width="32%" src="data:image/png;base64,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" /></div>';
		}

		//Block 5: Kontakt
		$blKontakt = "";
			
			$blKontakt .= '<h3>Kontakt</h3><table style="border:none">';
			
			//Email anzeigen:
			$blKontakt .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Email:</td><td style="border:none"><a href="mailto:' . $thisuser['email'] . '">' . $thisuser['email'] . '</a></td></tr>';
			
			//Telefonnummer bearbeiten:
			$blKontakt .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Telefon:</td><td style="border:none"> <input type="text" name="txtTelNr" placeholder="z.B. 051112345678" value="' . $thisuser['telefonnumber'] . '"></td></tr>';
			
			//Messengernummer bearbeiten:
			$blKontakt .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Messenger:</td><td style="border:none"><input type="text" name="txtMsgNr" value="' . $thisuser['messengernumber'] . '"></td></tr>';

			$blKontakt .= "</table>";

			//Temporär, aber es ist spät und nichts funkioniert gerade
			$blKontakt .= '<p /><p /><h3>Sichtbarkeitsstring</h3> (temporäre Lösung, setz ich mich nochmal dran): ';
			$blKontakt .= '<br><input type="text" name="tmpPrivacy" value="' . $thisuser['privacykey'] . '">';


		$form_bottom = '<p /><p /><input type=submit value="Änderungen speichern"><input type="hidden" name="action" value="save"></form>';

	} else {
		//Zeige das Profil ohne Änderungsmöglichkeit an

		//Ggf. Speichern / Verwerfen der geänderten Werte
		if(isset($_POST['action']) && ($_POST['action'] == 'save')) 
		{
			// Nutzerdaten überschreiben:
			$thisuser['birthday'] = $_POST['txtGeburtstag'];
			move_uploaded_file($_FILES['neuerAvatar']['tmp_name'],'./img/tmp/avatar_' . $thisuser['idUser']);
			$thisuser['avatar'] = (!isset($_FILES['neuerAvatar']['name']))?$thisuser['avatar']:'data: ' . mime_content_type('./img/tmp/avatar_' . $thisuser['idUser']) . ';base64,' . base64_encode(file_get_contents('./img/tmp/avatar_' . $thisuser['idUser']));
			unlink('./img/tmp/avatar_' . $thisuser['idUser']); //Später kann diese Zeile ggf. gelöscht werden
			$thisuser['street'] = $_POST['txtStrasse'];
			$thisuser['housenumber'] = $_POST['txtHausnummer'];
			$thisuser['messengernumber'] = $_POST['txtMsgNr'];
			$thisuser['telefonnumber'] = $_POST['txtTelNr'];
			$thisuser['hobbys'] = $_POST['txtHobbys'];
			$thisuser['description'] = $_POST['txtBeschreibung'];
			$thisuser['postalcode'] = $_POST['txtPostleitzahl'];

			//Temporär:
			$thisuser['privacykey'] = $_POST['tmpPrivacy'];
			
			//Änderungen speichern
			update_user($thisuser);
		}

		//Anzeige des eigentlichen Nutzerprofils

		$form_head = '<form action="" method="post">';

		//Block 0: Avatar
		$blAvatar = '<div align=center><img id="avatar" src="' . $thisuser['avatar'] . '" width="150px"></div>';

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
			if ($shHobbys) $blUeber .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Über mich:</td><td style="border:none">' . $thisuser['description'] . '</td></tr>';

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
			if ($shStrasse || $shHausnummer) {
				$blAdresse .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">Adresse:</td><td style="border:none">';
				$blAdresse .= ($shStrasse?$thisuser['street']:'Geheime Geheimstraße') . ' ';
				if ($shHausnummer) $blAdresse .= $thisuser['housenumber'];
				$blAdresse .= "</td></tr>";
			}

			//PLZ/Ort anzeigen:
			if ($shPlzOrt) $blAdresse .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">PLZ/Ort:</td><td style="border:none">' . $thisuser['postalcode'] . ' / ' . $thisuser['place'] . '</td></tr>';

			$blAdresse .= "</table>";

			//Kartenansicht
			if ($shStrasse && $shHausnummer && $shPlzOrt) {

				//HIER DEN CODE FÜR DIE KARTE EINBINDEN UND AN $blAdresse KONKATENIEREN!

				//Die folgende Zeile ist nur ein Platzhalter und kann anschließend gelöscht werden:
				$blAdresse .= '<div align=center><img id="map" width="32%" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKEAAACgCAYAAABkDQwTAAAYN2lDQ1BJQ0MgUHJvZmlsZQAAWIWVeQdUFE3Tbs/OBhZ2yTnnnHOQnJPkjMKypCVLBkEQEAVUMKECgiQVJQgGEBEBCaIIEgQEA6CoKCoGFCT9A6jv973/Pfee2+fMzEN1dfXT3dXVXSwAXMykiIgQFD0AoWHRkfamBvyubu78uCmABzyAGogAGRI5KkLf1tYKIOXP97/Lj1EAbX6HZTZt/e/6/2th8PWLIgMA2SLYxzeKHIrg6wCg2ckRkdEAYPoQuVBcdMQmXkAwcyRCEAAsehMHbGP2TeyzjaW3dBztDRGsBwAVgUSKDACAdpM3fyw5ALFDi3DEMob5UsIQ1VQE65ADSb4AcLYhOtKhoeGbeB7B4j7/YSfgv2z6/LVJIgX8xdtj2SpURpSoiBBSwv/ndPy/S2hIzJ8+BJGHEBhpZr85ZmTeLgWHW25iAoJbwnx22iCYEcH3Kb5b+pt4IjDGzOm3/jw5yhCZM8AKAAr4kowsEcyNYNaYYCf931iRFLnVFtFH7aREmzv+xj6R4fa/7aNiw0J2Wv22cyjQz/wPLvaLMnb4o+NPMTFHMOJpqOuJgY4u2zxRnbEU550IpkXw46hgB8vfbV8kBhru/KMTGWO/yVkYwd/9I03st3Vg9tCoP+OCZcmkrb4QX4D1ogMdzbbbwq5+Ua5Wfzj4+hkZb3OAff3CnH5zgxHvMrD/3TYzIsT2tz5c7Bdiar89z/DVqFiHP22HohEH254HeCaIZGH7u68fEdG2jtvc0ChgBQyBEeAHMcjjA8JBEKD0zzfOI39t15gAEogEAcAPyPyW/GnhslUThrwdQCL4iCA/EPW3ncFWrR+IReRrf6Xbbxngv1Ubu9UiGLxBcCiaE62D1kJbIW895FFEq6M1/rTjp/vTK9YYa4Q1w5pgJf7yICOsQ5AnElD+DzJL5OuHjG6TS9ifMfxjD/MGM4iZwTzBTGGeAmfwesvKby0vSlrkv5jzA2swhVgz+T06H8Tm3B8dtCjCWgVtgNZG+CPc0axoTiCDVkZGoo/WRcamgkj/k2HMX27/zOW/+9tk/Z/j+S2nlaRV+c3C5+/KGP7V+rcVw/+YI1/ka/lvTfgQfA3ugdvhXrgFbgT88F24Ce6D72ziv57wessT/vRmv8UtGLFD+aMjf0V+Tn71X32Tfve/OV9R0X7x0ZubwTA8IiGSEhAYza+PRGM/fvMwsqw0v6K8gioAm7F9O3R8s9+K2RDrwD8yyigAqvWIcPwfWQDiz80zAOCt/pGJVCPbFYmd9/HkmMjYbdlmOAYY5MSgQ3YFB+AFQkAcGY8iUAVaQA8YAwtgAxyBG9iNzHggCEU4x4EksB9kghyQB06BAlACysElUAMaQCNoAe2gGzwEj8ETMIn4xSz4ABbAD7ACQRAOIkJMEAfEB4lAUpAipA7pQMaQFWQPuUHeUAAUBsVASVA6lAMdhwqgUqgKqoduQe1QLzQIPYWmoTnoK/QLBaMIKGYUD0oUJYdSR+mjLFGOqF2oANQeVCIqA3UUdQZVhqpG3US1ox6inqCmUB9QizCAaWBWWACWgdVhQ9gGdof94Uh4H5wN58NlcC3cjKzzMDwFz8PLaCyaCc2PlkF80wzthCaj96D3oQ+jC9CX0DfRnehh9DR6Ab2OIWK4MVIYTYw5xhUTgInDZGLyMRcwNzBdyL6ZxfzAYrGsWDGsGrIv3bBB2L3Yw9hz2DpsG3YQ+wq7iMPhOHBSOG2cDY6Ei8Zl4s7iqnF3cUO4WdwSFQ0VH5UilQmVO1UYVRpVPtVlqlaqIaq3VCt4erwIXhNvg/fFJ+Bz8RX4ZvwAfha/Qs1ALUatTe1IHUS9n/oMdS11F/Uz6m80NDSCNBo0djQUmlSaMzRXae7TTNMsExgJkgRDgichhnCUcJHQRnhK+EYkEkWJekR3YjTxKLGKeI/4grhEy0QrS2tO60ubQltIe5N2iPYTHZ5OhE6fbjddIl0+3TW6Abp5ejy9KL0hPYl+H30h/S36MfpFBiYGBQYbhlCGwwyXGXoZ3jHiGEUZjRl9GTMYyxnvMb5igpmEmAyZyEzpTBVMXUyzzFhmMWZz5iDmHOYa5n7mBRZGFmUWZ5Z4lkKWOyxTrDCrKKs5awhrLmsD6yjrLzYeNn02P7Ystlq2Ibaf7Fzseux+7NnsdexP2H9x8HMYcwRzHONo5HjOieaU5LTjjOMs5uzinOdi5tLiInNlczVwTXCjuCW57bn3cpdz93Ev8vDymPJE8Jzlucczz8vKq8cbxHuSt5V3jo+JT4ePwneS7y7fe34Wfn3+EP4z/J38CwLcAmYCMQKlAv0CK4Jigk6CaYJ1gs+FqIXUhfyFTgp1CC0I8wlbCycJXxGeEMGLqIsEipwW6RH5KSom6iJ6ULRR9J0Yu5i5WKLYFbFn4kRxXfE94mXiIxJYCXWJYIlzEo8lUZIqkoGShZIDUigpVSmK1DmpQWmMtIZ0mHSZ9JgMQUZfJlbmisy0LKuslWyabKPsJzlhOXe5Y3I9cuvyKvIh8hXykwqMChYKaQrNCl8VJRXJioWKI0pEJROlFKUmpS/KUsp+ysXK4ypMKtYqB1U6VNZU1VQjVWtV59SE1bzVitTG1JnVbdUPq9/XwGgYaKRotGgsa6pqRms2aH7WktEK1rqs9W6H2A6/HRU7XmkLapO0S7WndPh1vHXO60zpCuiSdMt0Z/SE9Hz1Lui91ZfQD9Kv1v9kIG8QaXDD4KehpmGyYZsRbGRqlG3Ub8xo7GRcYPzCRNAkwOSKyYKpiule0zYzjJml2TGzMXMec7J5lfmChZpFskWnJcHSwbLAcsZK0irSqtkaZW1hfcL62U6RnWE7G22AjbnNCZvntmK2e2xv22HtbO0K7d7YK9gn2fc4MDl4OVx2+OFo4JjrOOkk7hTj1OFM5+zpXOX808XI5bjLlKuca7LrQzdON4pbkzvO3dn9gvuih7HHKY9ZTxXPTM/RXWK74nf17ubcHbL7jhedF8nrmjfG28X7svcqyYZURlr0Mfcp8lkgG5JPkz/46vme9J3z0/Y77vfWX9v/uP+7AO2AEwFzgbqB+YHzFENKAeVLkFlQSdDPYJvgi8EbIS4hdaFUod6ht8IYw4LDOsN5w+PDByOkIjIjpvZo7jm1ZyHSMvJCFBS1K6opmhm55vTFiMcciJmO1YktjF2Kc467Fs8QHxbflyCZkJXwNtEksXIvei95b0eSQNL+pOlk/eTSfdA+n30dKUIpGSmzqaapl/ZT7w/e/yhNPu142vd0l/TmDJ6M1IxXB0wPXMmkzYzMHDuodbDkEPoQ5VB/llLW2az1bN/sBznyOfk5q4fJhx8cUThy5sjGUf+j/bmqucV52LywvNFjuscuHWc4nnj81QnrEzdP8p/MPvn9lNep3nzl/JLT1KdjTk+dsTrTdFb4bN7Z1YLAgieFBoV1RdxFWUU/z/meGyrWK64t4SnJKfl1nnJ+vNS09GaZaFl+ObY8tvxNhXNFT6V6ZdUFzgs5F9Yuhl2cumR/qbNKrarqMvfl3CuoKzFX5qo9qx/XGNU01crUltax1uVcBVdjrr6v964fbbBs6Limfq32usj1ohtMN7JvQjcTbi40BjZONbk1Dd6yuNXRrNV847bs7YstAi2Fd1ju5LZSt2a0btxNvLvYFtE23x7Q/qrDq2Pynuu9kU67zv4uy6773Sbd93r0e+7e177f0qvZe+uB+oPGh6oPb/ap9N14pPLoRr9q/80BtYGmxxqPmwd3DLYO6Q61DxsNd4+Yjzx8svPJ4KjT6PiY59jUuO/4u6chT79MxE6sTKY+wzzLfk7/PP8F94uylxIv66ZUp+5MG033zTjMTL4iv/rwOur16mzGG+Kb/Ld8b6veKb5rmTOZe/ze4/3sh4gPK/OZHxk+Fn0S/3T9s97nvgXXhdkvkV82vh7+xvHt4nfl7x2LtosvfoT+WPmZvcSxdGlZfbnnl8uvtytxq7jVM2sSa83rluvPNkI3NiJIkaStqwCMPCh/fwC+XgSA6AYA02MAqGm3c6/fBYY2Uw4AnCFZ6AOqE45Ci6DfY0qxXjgB3CRVGT6IWpF6lWaAUEKMpt1JJ0GPpZ9h6GK8wJTFHM7izGrM5sIeypHJeZ6rmXuIZ54Pzy8soC/oLZQsXChyS3RC7JcEl6SOlI90ukyV7IDcNwV2RV0lsnKOSr3qoNonDaKmpJbJDh/tfToFutf1+vXfGqwbsRvLmhiZupgFmydZHLUstqq1vrOzz2bC9o3ddwfIkeDE5sztwucq5CbmLu2h6Km5y3C3pZeTN5kU7rOPfMS3xK/evytgInAhiCqYP0Qj1CEsPDw7onJPe+SLqJUY9liVOMf4PQl5iXV7B5I+76NPUUp12h+fVpTenvEmk3BQ6ZB7Vlp2Vc7w4dWjork2eQnHKo4/OvH5FF2+wmmnM/FniwraC9+eIxarlHieTy+9XDZY/rOS+4L+Rb9LB6suXe658rp6o5a9Tv6qab1nQ+S1rOvFN67ebGm819R9617z7ds1LQV39reS7+q1sbW9b7/Vsf+eaSe+80FXZrd+90rP9fvBvYK9Ew+OPbTqI/QNPsrvdx/gHZh5XDHoPyQ+NDd8eSToieSTD6NXxoLHpcc/Pq2b2DOpPLn0rOX5/hcmL4kvR6YKpnfPCM7Mvbrx+uCs1xvtt0Lv6Ocw71EfqOe5Pqp98vh8cKH5y/dvyt/jF1t/4pbslot+vVmVXYtZb97Y2Fp/Iegqyg1mgBvQHhhqTA3WFbnV1FGR8Oz4h9QZNAYEDOEe8QCtOR0t3Th9GUMIoxoTjuk5cx9LN2sb2x32Jo5rnFe5qrkv8lTwlvOV85cJlAqWCVUIXxSpEq0Rqxe/LtEs2S7VJf1AZkh2XO65/AuF54rPlCaUx1SeqA6rDag/0OjSbNe6veO6do1OhW6BXq5+ukGcYZDRLuOdJnqmCmb85vQWwGLB8plVl3X1zhM2e2197Mzt5R04HCHHOach59sula65bonufh42njt2ie1m8oK8PnlPknp9GsmVvif8Mvz3B6QFplPSg9KC00PSQtPD0sPTItL2pEWmRaVF749JjU2NS4lPSdiXmLw3KSkpee++xJSE1HjEO3LTKzNaDoxkfjgEZ3FmK+aYHfY+Enf0cG5FXvOxx8ffnFg9xZAvdlr7jN1Z/4KkwmNFFeeaiwdKXp3/WUYoF6hQrbS4sPtiJOIhhZdrr7RXj9S8rf11lVDP2yB3Tf+6/Q3yzajGjKaTtyqRCNbZMnznVev7u4/batqzOwLuGXXyd652jXdf6zl6n9Jr+IDnwY+HA30XH6X0Ow/IPEY/nhisH8oc9hxReIJ5MjlaP5Y9TnlqOaE4yfeM6TndC6aXAlNa094zJ16NzIq/OfwOzGV9EJx/9Clrwe6r+HeaxaWfn5ffr3xc+7a1/lKgE7KExlEeqI9wMLyETsOwY8qwKtiHyI12jaoQr4Ofoj5Io0jzkpBD3EGcpz1HZ09PQ9/FcJTRi0mBGc08wlLJGs9mzc7HvsjxgLOUK57bhkecF+Kd4LvGnysQJGgiJCS0jtyjmkTzxaLFbSXEJVYlB6UqpRNkrGUFZL/ItcsfU/BWlFVcVupA4oODKrvqpFqJOklDUGNas0Rr9w6uHWPaJ3RsdIm6Q3oF+mQDaYNvhreNMoytTJhNJk3LkXihaL5s0WZ50MrGmhW5T5TZUGxlbb/bNdunOBg7Ujv2Ox1zdnRhc5lwLXbzcRd3/+Rx0zN1l8Vu1t2vkXtABsnFR5qMIk/4XvfL8w8NsAyUotBQPgY9Dr4ekh8aF+YarhnBFbG252Vke1R5dGYMJdY6TiGeNX4lYSbxwd6GpMLkA/siUjxTzferpgmlM2ZAGV8OvMmcPTh36FPW1+wfOb8Orx9F5WLz8MeIx+lPMJ9kO8WZz3ta4IzwWbECyUKZIoVzysVqJVrndUr1yyzLyRX7K0sutF6cuLR0mfWKcrVdTWhtdt3Fq531Uw2r19luKN20aQxqOnCrtLnl9mjLl1bCXdE2vfZdHXvvne6s7eruft7zvZfugdxDp74Dj1oHsI+9BnuGLUdmRovG4yYSn114iZ+ufn367eCHmM+53/WWqzfXf/t/cJsFi2SnlbpIQEDODYcyAMpbkDxTHTk/KgGwJQLgqAFQjokAetEEILezf88PCEk8qQA9knGKACUkJ3ZGsuY0JJe8AQbBZ4gOUoAcoUQkB3wALaK4UAaoINQJVCvqPcwOm8JxcBX8DE2PNkEnIznZApKHBSK51yxWBBuIvYL9jFPBJeO6qeipPKmqqH7izfBF+K/U5tRl1Gs07jRNBHZCIuEF0YhYTctKu5/2M50X3RC9Cf0dBlWGekZZxlomOaZrzBrMHSyWLOOsAaxLbHnskuxdHD6cEOKlBlyz3Nk88jyjvCl84nzD/PsEpASeCh4S0hB6L3xOxE4UJ9oqFisuLz4vUSUZICUm9V66RiZKVkMOJdcnf0bBV1FJCVYaUb6gkqhqqyaqtq4+ptGgeVQreIeFtqQOQeeT7rBek/55gyzDaCNvY2sTQ1MdMw1zZQsFS3kreWuFnYo2qrZadvr25g4Ojl5Ooc7JLnmulW4t7mMei7tYd2t6kb2Pklp9vvqK+5H9zwe8pPAGkYNrQkGYR/jdPTKRldGSMbfj3BKwifeS8vaFpHqmeWQEZGYcqs5+foQ91/lY4YmhU0tn+AtsijKLO0upyu0qyy7+vOxQ3VDHUp907dVNm6bbtyXunG2j7kjqXOzZ17vRt6d/aFBomPQkd6z66a3J68/LXqZOO77iff3yTcE7m7mND9UfXT+jF2q/un5HL9b/JC0z/+pdTV/X34ofEMAAGsAM+IEc0EdWPxQcBOWgHcxAGEgKsoeSkOx/DIVFKSC5fQ6qGTUP88GOcA7cCa+jNdFx6Eb0EkYLk4LpwhKxztgyZNW1cUdwU1TKVFlU03gt/Fn8MrUHdRuNGE0uzS9CIGGcaEFspVWlraOTprtCL0PfwKDJ0MloxzjNFMVMxVzKooWsdjySYd5nj+UQ4RjnPMJlzLXOfZsnkVeLd52vk/+wgLOgkOAXoXvC+SLBooZiPGK/xJ9K3JYsloqTtpGRlMXJvpPrla9VOKmYrERRdlExVdVQk1UX1eDX5Nbi3MGlzacjoiujp6ZvZOBo6G+UaJxrkmd60uyMebHFRct6q1brvp3Pbb7YYey5HdQc7ZwinPNcGlxH3dY8xDztdqXsrvOaJrH4WJIP+N71WwnQCkyi3A1Gh1iFngqbjpDfsz9yOFocOZEm49US8hOXkjyT76VIp55Jw6bHZXzIJB18muWYPXjY9shIrlve1HHKSe180TNMBXDh8rmvJZ9Lv5YvX0BfYrksWW1U63v1YMPV6y8bGW6Z3c6409VG0+HYWdz9spf1ofGjwIHkwYzhlCeBY4ZPiRO9z2JeML8smxaeKXyNm/V/0/qOOOfw/tSH/o/oT6qffRYOf7n6deTbt0XGHzI/TZdIy3t/HV+pWr27Nrr+fmv9UcjuZwQCyN63AL7Izi8D3WAeYoH0oQioDBpFEVC6qBhUDeodLAr7wZfgebQSOgl9H8OK8cfcwtJi/bB3cVy4ROTOqUNVgSfi9+I/UZOpn9G40owRPAgzxFDiKm0enQRdNz2FgYHhDmM4kyjTNHMZSwCrIusaWwd7NocTpwjnElc/dxXPQV4Knw2/uoCoIJsQQRgrAotixKjFmSUEJJWkLKUpMlmyNXIj8quKokp2yvtUrqg+VafSUNP00zq1o0t7UVdUz1U/x6DN8IextEmg6WWzjxZKlslWvTvZbYJsW+2ZHUIde5yFXdJcp90NPSp34XeHe42SdHyqfFn90v2/BfpReoIFQpJDJ8N3RJRG4qLCoydjzeOaE2QSy5O4kvNTmFJPpLGkFxwQzKw+pJbVneN8+P3R1DyOYw0n9E/ezlc53XBWvuBakeq5lhLD84/K3MvnKhMvEi+VX9a6MloTVcd49VqDy7X1GxcabZvWmmtadrcy3u1tT7u3o/N7d/X9kAeqfdCj/oFzg5RhpZHF0drxXRPoyaLnoi8qptim42b6XrPP2r5Jf1v57u7cw/cDH+7P3/lY+inzs+uC+ML3L/Vfw76JfHv8fe+i8OKdH84/Fn7uX8IvHVvmXC78xfgrewVaSViZXbVevbHGs3ZgbW5df71g/duG9caFzfWP8ldS3Do+IIIBAJgXGxvfRAHAHQdg7djGxkrZxsZaOZJsPAOgLWT7d52ts4YegKKeTdTd2Zj6799X/gejIdAfMjeBzwAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB+AKHQ8fMNKMX84AAB7xSURBVHja7V3Jj9zWnf64FMkia+mq7tZmyUK3HctSYsvZLakdO8l/MIe5BJjjXAe5zWUuc5qbMf/FAIP5A8YIgkSxYCsOFEmBDTmy0GrJsnpTd9fCKm5FzkH4vWFV10Ky+Nhkqx9QkCzX8vje977f8n6LsL29HSDmWFpawu7uLsrlMjzPg+/7aDQa2N3dRRFHo9GA53nodDqpfacgCJBlGZIksT/pRSMIAnieh8FgwP6k17QhyzIajQYODg7gum5qc15YWEAQBGi1Wpmuv5zkQ57nQdM0WJaFWq2GnZ0dNJtNSJI0cwHzODzPgyzLqYFNlmWIosje4/s+BoMBHMdhIKPDm6cxGAxQKpUy/925QNhqtbC0tMROoyzLhQSh67rQNA2CICAIpgsGURRRKpVQKpUgy/LQphHYbNseYjdeYJs11yQg1DStOCAkUUwbQ2xi23YhmZAO0ah4kySJga5UKjFx6vs+PM+DaZpwXZcr2MaxLy8mpGfOkkzkeTaNTqOu6+j3+1BVtZA6oed5CIKAsVqY5UisDgYDuK6Lfr8P13WH1uC4jEKCkPTCcrkM0zRhGEahFl0QBMZwgiAMzd/zPNi2Ddd14bpu7vQ3XuKYQJh7cTxqnNTrdezs7EAURYiimMsNC+tzxHLEfEEQsA1tt9twXTf1DS6COA6CAL7vDxlVuQehqqpotVpDk5ZlGY7j5Ap0YUOCFtt1XabPua4LXdeh63pu5n6UIrlQTGgYBra2tgAAqqrCsqwjBeE0I8J1XViWNVGfGwwGEAShUG4mHmzt+36xQBgWC+VyGf1+H7quZzf5kFgdZ0T0ej1muUZZfGLPvIOQlzimtVMUpTggDLs1yuUydnd3UavVuE02DDgyJtIyIsJKeZq3EEUUx4IgZKrbJwZhEATMuWlZFqrVKlzXhSRJkZy+cSxXYjwCXVi0pmW5+r6PIAgyF0V5E8fhw5h7EIYt5H6/j2q1ykTZOKdvFCNiVLzSCPvneFquR6GU500cE/B4/gYXEO7v7zO9sNfrRQJhHMv1OFuGeWXXLN00c4PQMAxG4bquwzTNsXrhPJZrliDMWinPmzim7ywUE9KEfd9nxkmz2UzVcj3OSnnexDE57gsDQt/34fs+M050XUcQBCzebdRypaiSPFuGWSvleWXYwohjApmqqrBtG7quo1QqwXEcBEGAbrdbqM0M+wpfRVEc/u4smTDRasuyDE3TUK1W2d8tywLw/0ENJKKLNI7qAj9P4pgOY67EsSzLTL+jv4c3jQJCKY5QVVX0+31UKpVCiqITC/mIxfEswJF+53neUHh6o9FgPkJy2YiiWMhw/6KAkLc4zhSEJFKjAm6SXhh2zxDwihjuPxgMch+cm4U4TppzkwiEpVIpFuCmgbDdbjMfIVnJRQv3HwwGEEUxlavHE3EcEYR7e3tzf4nrumg0Gtje3h4yTrI8TTyMkzyH8L/y1vE4JiQxQeLMtu0jSR9MC4RFcNPwFMdZiP1UQUhsqOs6er3ekJumaJYmbUCe580bHFnfH6f2K3SP3Ov1GBMCKBwbUp5F3g8Pb3GcFROKogg5TRBWq1Vsb29DFEX2IEXVC19lXyFvcSzLMhRFgaIoL92BaYrj8EOQYl9UEOaVwcn/yjuIIU1xLAgCA5yiKJAkCUEQwHEcdDqd9EA4GAwQBMEhkVy0XGR6lqMohzFu80YvEIih8y6Oac7EeLSujuOwF3tvmpMn48Q0TWacGIYBWZYLVbHgyCoRTLmxcl0XjuOwgJE8RVfPSsUwTZPNfexzpzl5clrv7OygXq+j2+0y4+QEhMODqneFARdO3PI8b2KgbxaBt9Mc1hSgHJ4/gdfzPBYrSuVVZh6+tEFIC6koCqPcoumFafsKw5Uf6BUO8o27cVnc5oQjaUZZblx9Hjo4iSRA2iAEwPyFYTHzqrhpSDSFATdaySvMcEnC3bIAoSAIUFUV5XJ5SCWwLAue56Van0dOm0F832d6IQW4KopSqLtYz/OwubmJnZ0d7OzswLIs9Pt9tgEEJqrkpaoqFEWBpmnQNA2qqmJhYQFLS0uoVqvsO9MU7VmtZbfbZaDjpgvz2EBd17G7u4tqtQrLsqAoSqI00CxBt7u7i62tLWxvb6PVakVWyqNkBDYaDTSbTSwuLuL06dNz63RZHGjKIe/3+/wNMh6Tp0WmAFcSyXkCYRAEePHiBZ48eYInT54MsVTaluf+/j729/fx6NEjCIKAc+fO4eLFizhz5kyi38riJsP3/cx8pVyYEHiZg+z7Pgv7Nwwjk1MVBXzPnz/HgwcPWL501r//7NkzPHv2DNVqFZcuXcKFCxdiG0FZGCZZ3R1zA6Gu67AsizEMOV6P0lXTarVw584dpBG+lsbodDr4y1/+gr///e947733sLy8nBtxHE764p0rJPKaPN2chE9tlhW7JEmCqqowDAOGYeDBgwf43e9+lxsAhke73cbNmzdx586dSIc0K3GcppsqUyYkdwQFMYRLCquqOhRhk9pDhNwho47fg4MDfPLJJ7kE3+hYX1/H7u4url27xqzqoxTHhQchANaWwbZt5m/SdT0xCOnyfhRw4YXzPI85T7e2tnDz5s1ClXrrdDr4wx/+gLW1NVZA4CiZMIvfSh2E4ZNjGAa63S4zToi1qKDmLDCHnb1hpy8wOxlrd3cXn376aSH7qjiOg5s3b+LDDz/EwsLCWACeMGFEJtR1fawYJKMlHHM4SZxS663R66Fpm7C9vY1bt24VupSH53m4desWPvroo7GRSFk4q7OykLkyIcWNjXsPnfBp4pTYLq4FnBYABUHA0tISarUams0mK3NC4UkUE+c4DlzXRbfbZT7BVqs1N1Asy8Lnn3+Ojz76iB3uLBOQCgvC0ftWMkxGA1wlSYLjOHOlmo5jj9u3b8/9Pc1mE6urq1hdXUWz2cTe3t5EsS7LMrP6l5eXsbKywuby9OlTrK+vz+WPPDg4wNdff40rV65kKo7pNwrPhARCAtso67Xb7VQX7M6dO3N16jx16hTeffdd1Ov1oQ1PEtIlyzJWVlawsrKCg4MD3L9/Hzs7O4nm9eDBA7z++utDpVWyEsdZpDmkDvPRzaKwpbBxMo4x5x1Pnz7F06dPE31W0zT8/Oc/x9raGgNg+FnmnevCwgI++OAD/OQnP0l0bxwEAb788stjK44zASGAsW6ZtB7Q8zz87W9/S/TZer2OX/3qVzh//vyhDU4z804QBFy8eBG//OUvh8Kjoo5vv/0WnU4nU3FcWBCOGhKyLDMFfpb+mHQ8fPjwENNGGcvLy/jwww+ngoLKgqQ1KpUKfvGLXyTKYXn8+HHm4jgLN00mTDhJn0rj4SzLwtdff52IAa9fvz4zUoRH+melUsH169djf25jYyPTmMxwMG6hmZAGBbimzYSPHj2KbTSUSiVcu3YtUsQ3L+W80WjgzTffjPUZ27bRarUyY8Kswu9SByE12Rmn/I+KzHmZcDAYYH19PfbnfvrTn0ZORQ0XU097XLlyJbah8vz588xAWCqVignCcSKZEn1GjZN5GWZrayv2PfSZM2dw9uzZ2M/Cgw1LpRLOnz8f6zNZxUDSrVUWoXditVqFYRgsyoV6jMzjChhnnEiSlDoTbmxsxP7Mu+++m+hA8fKXvfbaa7HeT75V3kxIqkoWTCgTQMaBjlwUlMA06TW6IKMgJOV/lCHn2Vjf97G5uRnrMxcuXJgZIjXud3jeHCwtLcUK9m2325mJYtp37iAkeie9Z9KLnM7jNoPASq9xp4oWOVyAURCExFHCvV4v9gK98cYbiQHPiwlFUUS5XI5800OkkIU4zioKXh41KKI8IMX1jQMr/fvoAzmOMxTgGmbDJA8bV12gIISkOi7P66s4IATAvak5pWIk8b3OBcK4zDCLhRYXFxkYZVmGaZpjT1dSMafreqwo7bfeeiuxnsu7mHpcxzVvJqT5ZMWE3FzhYZAS+MbVpEkKQkEQcPXq1UjvNQwjthU6uulUTJ3HiKuO8KxFI0kSDMNgreByy4RxF5bAR4yYlnFy4cIFuK6Le/fuTWRmRVFw7dq1uX4nXJuGBwvFEa+SJHFVDch/SsWs0hbzNP/wKxMQTlN257U6V1dXcebMGWxsbOD58+fo9Xosp+Xs2bO4dOnS3Fl+4TrWPEAYZ8MpmJaXGFZVda6ehAQ0sg/CLzrQ9LJt+2WT9izE8SgjpglC0g8vX76My5cvc3kWnr5Cy7IOSYdpIxxqljZLGYYBx3EiFSkIgysMOOrgQFY8FVGiIqpHJo7D1uwoOIvSUZOXm+a7776L9f7l5WUuTFipVCCK4hArj2OzcLs4enmeB8dxEvsVMwPhpEKZRSlQzsNNEwQBHj16FOszp06dSv3ZVFVlEfC6rjPAEZvRax6gHQkIRyfKw2GdNQjTrrP4+PHjWCkOoiji7Nmzqa+VYRjwfZ+BLKq/uFDW8Tg3TTiOj5fVmfahSpMJTdOMHQ1+/vx5qKqa6loZhgFJknBwcHBkRQLELEHouu5YC7kIIjnNkC7btnHr1q3Ym76yspKqr5IyBfv9/pFWqcjEWR1mwnGBkkUwTtKykB3HwWeffRY7K3BxcRGLi4upqi4khuNY54VmwrAY5uGmKQIILcvCzZs38eLFi9iffe+991JNctI0DYqiwDTNI9fHMzdMZFk+5IcqAgjnDek6ODjAZ599NlRQPupYXV1lFSvSEMdhn2BWQQq5MEzGWcmvgpsmCAKsr6/j7t27iRinXq/jnXfeGQLQvMylaRpEUTxyMZw5CCnvFjgcTVyU3sJxLWTP83Dnzp3ESfl07z3qGpoXhKQS5aXBEfcGI//xn5/i44//NPb//fa3H+Bf/2WtMCCME9LVarVw+/btxGVJRFHE+++/fyghKw1xPC7f51gaJjQ+/vhPePvSEv7nv37D/u1//us3ePvSEgMnzzCptEEYZa4bGxv4/e9/PxcAb9y4caiGdRprRBHyeSocmkmrpXpdw9r7r7P/Xnv/ddTr2qGFz7vDOmwhjxNlruvir3/9a2LxS989DoBpqS90SZArEC4uLiIIAvT7/ZkJTFwpuSC3JpNAsLe3hz//+c9zKfu6ruP69esTI2UIQPPocqQP5qmAqEy3AIZhTCwIFOWVBgiLII5HrXnf9/HNN98kLshEo9ls4tq1a2ND/QVBgKZpqFQqrMzecdEHAUB2XReCIODg4GBqtl04424aWKlQevi0Pnt2AAD493/75aF/K5KbZjSky7IsfPHFF9je3p7re1dWVnD16tVDa6AoCotwAV7etlAZkOOiDx7SCaOy2rT00NEO5QDw7bMu/umf/xv/+A8vE8//6Z//G98+6xbSTUO+wq2tLXzxxRdzs9KPf/zjIZeVJEkswpl0T9M0WRRyGtImb72n5SR+p6jpoaqq4re//QAff/wn/O8n3+B/P/nmkItmVN8qAgg3NjZw69atuXTmZrOJn/3sZ6hUKqxTKDFeEASwbRudTocLa+XNEyE4jhMEQTAXzU9TtKMWHup0Orm4Qpo1Njc3cevWrblE+ve//3388Ic/hKZpzNigGEvbtlMtozzKvAsLC9jf388VG3J10YRPcRAEePLkCS5evAjbtrG3tzdUmKgITLizszMXAMvlMn7961/j/PnzCIIAruvCNE3WrnZxcTETD0HemDCROI7r0kj7vZMWlaK0Z/09znvp751OB7dv3048x9deew1ra2tQFGViACmP6O1RFs7jkHmeijgPHVbEp7VrIMOnVCqlctMSBAGb57i/k/77xz/+MbER8s477+DNN9+ceV87GAwy6TGcSybk9uUTTrVpmofqRI/mBpPhQwsW7vJEqYQE1llAmvb3KOPRo0fY2tqK/fyKouD69etYXFyMbPQkqWVd9MFVHE/y/bXbbVy4cIH9Nynj4R529ApfM1EYepb6o2mauH//fuzPVatV3LhxI7JhRiCkdeOhG76S4ngcCF3XZbmrNKgMxji3DwVgEnNmVTOPxoMHD2L/3sLCAtbW1mIXUSJRPa3Y/HEUx1w9xGGgmaYJVVXR6XRQq9WG3jetFksQBOh2u2i32xBFEY1GI7Pm3a7r4smTJ7E+YxgGbty4kaiKF++qsHllQpHn5MI64e7uLhYXF9Fut4eqpUYVr7ZtY39/H5ZlwTAM1Ot1rpYk8LJLVBwWlCQJa2trc+l1vGsh5pIJeU0onB7Z7XbZxkwSxVFPcqfTQbvdhizLaDQaibojRR1xa2L/4Ac/GOo/l2R4nvdqMmFWLBhXFE9jxb29PViWhUqlgnq9nvrGDQaDWJXy6/U6VldXU/ndV44JeZ0QWkhiQdd10W63hzqZJ+lnPIkVm81mqqzY6XRircvq6moqQRgUvV2UgI5ci2MyHnZ3d1Gv17G9vX0owSmN2sthXTFNVjw4OIi+iKI4VyXYrIyTV0oca5oGSZKwu7uLSqWCra0tnDp16tBtQFoFwH3fR6fTQafTQalUSkVXjBNE0Gw2Uyvhy9tCzqM45uKs1nUd7XYb/X5/oluFqkClOSzLguM4qFQqLEQqadXROCFUYRUjjQPl+z43yz+PbJi6ONY0Dbu7u+h0OiwyeFzOBK/Ea+ooT7/fbDYTuUzi6KppV0/lbZwca2f1YDDAixcvUCqVoOs6fN/H0tLSofdlUX7Csizs7e3BcRxUq1XUarVYyn4cEKZdTZ8nCHPJhGlNzLZtdLtdLCwswDRN9Pv9iY0MeVSGn8aK3W43NivGuTZLO/IlCzdN7nTCeTe61+thMBigXC6j2+1ieXl54mabppl5ame/32e6YrVaRalUgmmaU3XFOEyYtv4WvkPmEQGdJ3Gsqmryjk6Ueuj7PktwWlhYmBo14rpuoqpUabFLq9VCuVxGpVKBqqro9XoT5xNHOqS9qbOS7I+DOKamPbGs4zDwgiCAoiiQZRm+72N5eXlm98wgCHJRBarf72MwGKBSqcAwDCiKgl6vx71fXJ7cNEfNhOVyGbquwzRNWJZ1OOXTNE1IksQMizDwVFWFqqosQnhpaSmSZeh5HsujyIUiLIro9XoIggC6rqNer8O2bfR6vdwkAPHSC4+SCcMG6/7+PlOH5NETQr4913UZ8HRdh+M48DwP1WoV1Wo1soLfbrfhui63JjBJxAA1AKeuQnQyG40G+v0+er1eLIuXR685XoEMoigeCRBJ6pimeUjqyI7jwDRNxgKqqqJcLkOSJKbzqaqKpaWlWIvt+z729vYQBAEkScqNLkLNpMPGUb/fh2VZ0HUduq5D0zQsLy9HqqygKAqXSJ7BYMAF3FnX/Gk0Gqw676SAEHlnZweSJKFSqUAQBGa96rrOupLHddWME2t5AGGYBSfprATGt99+G1999dXMeV+4cIGLjhXuFpBWJDkxa1aR6dVqFbIsM4nquu7YZDGZgko1TUO5XB7qUxx30ahcxThFOA8gDCdKTXsOuoO+cePG1EoLzWZzqJQvL+MkbRDyZkJBEFCr1aAoCjqdDnOP1Wo19Hq9QwaqEKSADhLn00RguVzmVlkgjigmvSTqcF0XDx8+xN7eHkvGMgwDr7/+Os6fP88t5EoURSwuLqLb7UZqeBhlaJqGarWKFy9ecGNDVVWZVG2320P6H1XkcF0X3W73//2hSayrsLVLBsysk5H3tmHTgHvlyhVmvJBib9v23GXaZunUaXeRIt08bQBKksRq6dB8RwEIAL1eD67rolqtotFooN1uw7bt2SCkwFMCXBIXRp5AmHQe/X4f/X6fFS9SFAWapiEIAgZGx3FSfc603TRpGyVUPYwMKMuy0O12UavVIMvy2APqui729/exuLiIWq0G0zQPg5CARqBL69TkSSecZ4T9nSTeyX8KYAiQ865d2hUZ5tUvKQ+cDqIgCHBdF51Oh/mSCYyapk1Ue0iSUHFWOQgCRpO8nMnHgQmnAdI0TciyzFiBbo8cxxlSW5L4CjVNS239yO0WdciyzEqulEolxsqe5zG31jhmJd+rqqoTf880TdRqtZfiOIv73CJU5p930C0S3TgRIMN36eEm1eGWrpPYKe075FniOAw4qvVDzxY+ULPYlKTpNBBShFOtVnvJhFmIweMijqOKUQqOaDab7PqT/JTUUWnU2AsDMwzOWSCcVXEs7J4RRZHpcJIkQRAEBjp6r+u6sCwrsuE5blDOzzQ/JwExExDyqq2Sd91UEARIksSMmlFWCtfcoTo8465Dq9XqUGH70T/jjHHVKxzHYRcMSUE3TiRTtNI0F5Pv+5Cz8J4ripKLwIC4mzYYDLC1tYWtrS10u13Ytg1FUVCpVHD69GmcOXNmqvVKt03jDiC5YMbpimFg6rrOLPDwIYry5+i/jbb5rdVqGAwGXPy3vu/Dtm1omjbTzylnxQx5K9Y9a2xubuLu3btjLbydnR2sr6+jXC7j6tWrh1JZw2BK8uzhu23P86DreirR6KPzoK6rvIZt26jVaiiVSlMNMzELEKZ5/5mFOH748CE+/fTTmTcr/X4fn3/+Ob788suJTDhvnxfXdZnuxkN35ZlGEA6AmYoP3uCgxcsDCKNs5HfffYd79+7F2vQHDx6MrVuTli7sui63cDHSW3kN8hkeqTgmKzDN30kam0glNia5DQaDAe7evZuIde7fv4+zZ88OgWXSrUESEPLoxMS7KCexIYXHTcqwzAyEaTJhUv2oXC5Pncfz588TBws4joONjQ1873vfY88timIqurDjONxiFsO6Ky9bwHVdqKo6EYTcxTEPEI7606K+Zs1hc3NzbmNm1ChJg2Go2SUPIyKL9FLLsqAoysTf4W6Y5EknpIY1k0bS/sQ0wq4OAkxaXgHHcbhU9udZDzEskgFMNFDELMRxnizjaSCcN+Mu/HnSs9JaX9ILi8iEQRBMNVAyAWFegheoC+mkMe8mhz+fdvFzAiGPHGdeLqBRNpQkaayVz70SY5GYcN6C7OFgBR7VE3iI5CyME5r7YDAYK5K5g1AQhFyBcFo4/unTp+f6fvo8dZpK2+3BQyRT4EQWwR0kkkd/KxMmzFMs4bTFPnfuXOJNFkURFy9e5Dp/Hk7rIAiwt7eXSWECMlBGdUM5CxCmwYTVanXu0zorJEpVVVy5cgX37t2L/d1vvfVWrO5NSUUn5XHnJSop7vzHNZEsDAjTqGc4Kec4PN544w20Wi08fvw48veeO3cOly9fzmQjSSQXEYTkKxx1hXEFYZo+wjTERZTvEAQBP/rRj1CpVPDVV1/NnPvbb7+Ny5cvZ1Zt33XdqVdgeQagruvsBiUzEPK4N85iCIKAS5cu4eLFi1hfX8fm5ia63S5joUqlglOnTmFlZSWzFmdhK3NWBbQ8DUpmojYi465cMwFhEbq6jxuapuHy5cuZidq4IjkvVc4mDartA2BqEv8JCAs4yErOMwhrtRoL7e/1elMxwFWRydO98XEavO6R0xqqqrKaPlFaeHBnwqKW/8i7q4OCUfNmJauqyuIoo94YibxBeMKCfPXCogMwE3F8AsJXRyTTVWXcO/MTJjxhwtQGxSbGvdniDsITnZDPCIIg9YJJaeiqQPyInBMmPBHJqR6MJDUVT3TCgotkHqmg85AO8DKWMo5IFnkC8ASE/HUwKq5+1OCjrg8EvjgMLfI+FSc64fE1UARBYC00JElihZXipiJwc1aHi/DkqWUXj0EVtcJFJYkNeEe7kEjmVTt7EvionBwVawpfIRIIS6VSpL3nBkLf91k491E1VeTF8AQ0Al2Y9amKqaqqmYhJ13W5B9OGR7iW4aT6hUEQDAVZzJKGXK/tCITTKnbmedCJD7McWX4EOLodoFd4I6imIE+VxPd9Fq3Mu/KZoigMWLPAFYcNZd6nlFoGCIKQSjHxJMYRvSgBKcqLiliGjQAqrTwKuEnuE8Mwppa/SFsk8wYhlTWJImJJTFO0zzTAcg/vb7VarNk1MNySgjaTJhgVIFEBFVV3pReV2yB26ff7YyOBo1qunudBURTuIPR9H5VKBf1+nyvrxm1BEZUNuYMwCAJ0Oh30er0hXapSqST+vnEvAtCk/5704jkcx4Gu69xFcjiqhicbhhk3KhjDn5kkBbmDMLxQ1No1rORWq9Wh/sOzwFWkQWXReFuvrD0XZ72QmG1aj5JpbDhpDeSj3CTXdVm1rONkQYfBQVUHeIKQDmi1WmUSxvd9dLvd1N1jlmXBMAwYhhELiKQbjmND8ag3qsh976KKZN5Xa+QysSwLpmmydsH1ej2x2jNN/+x0OqzRZFQQBkEw0aku5mGjjjMIbdtmNwu8Rrlchud56HQ6rF1Fq9VCt9tl7YPTHuGOp1GBGPap5o4Jj/MgETSreHjSQd2jxlng1G19MBhwqfTabrchy3KkA0brMI4NT8RxRmzIiwmp0+gkN5DneWi1WqwVbNqj1+tFzr0mNhwN9ToRxxnpheF2XmmLYsuypq4hucnIUudhXEZhWnLTjLLhiTjOCIS+76cOAAqdilLsnVIwq9Vq6rWviQ2j3JWTPzPMhifiOEMgpq0XapoG27ZjOY673S7zzaY16HZpGhsKgsAOwOiBPBHHGeqFoiimFvunKApkWY59JWjbNvr9fur1bHq9HpvTOBdSvV5nffRG1+L/APJaxBOibOW/AAAAAElFTkSuQmCC" /></div>';
			}
		}

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

		$form_bottom = ((($_SESSION['user']===$thisuser['username']) && (!isset($_GET['view']) || $_GET['view'] != "public") )?'<p /><p /><input type="submit" value="Profil bearbeiten"><input type="hidden" name="action" value="edit"></form>':'</form>');
	}

?>

<!--Überschrift-->
<h1 id="profileheader"><?php echo $headline; ?></h1>

<!--Ggf. ausgeben des Links zur öffentlichen Ansicht-->
<?php echo $link?>

<!--Beginn des Formulars zum Ändern der Profileinstellungen:-->
<?php echo $form_head?>

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
?>

<!--Ende des Formulars zum Ändern der Profileinstellungen:-->
<?php echo $form_bottom?>

<?php require "./includes/_bottom.php"; ?>

