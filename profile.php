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

	function get_place($plz) {
		$db = db_connect();
		$sql = "SELECT place FROM Postalcode WHERE postalcode = ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('i', $plz);
		$stmt->execute();
		$result = $stmt->get_result();
		db_close($db);
		return $result->fetch_assoc()['place'];
	}

?>

<?php
/*
* @author: Nick Nolting, Alexander Gauggel
*/

	//Includes
	include "./includes/db_connector.php";
	include "./includes/session.php";	
	include "./includes/map.php";
	require "./includes/_top.php";

	//Profile sind nur für eingeloggte Nutzer sichtbar:
	if (!@$_SESSION['loggedIn']) die ('Profile sind nur für eingeloggte Nutzer sichtbar!<p/><a href="login.php">Zum Login</a>');	

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
		$blAdresse .= '<tr><td style="border:none;padding-right:10px;padding-bottom:15px">PLZ:</td><td style="border:none"><input type="text" name="txtPostleitzahl" placeholder="PLZ" value="' . (($thisuser['postalcode']!=0)?$thisuser['postalcode']:'') . '"></td></tr>';
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

		//Temporär, aber es ist spät und nichts funkioniert gerade
		$blKontakt .= '<p /><p /><h3>Sichtbarkeitsstring</h3> (temporäre Lösung, setz ich mich nochmal dran): ';
		$blKontakt .= '<br><input type="text" name="tmpPrivacy" value="' . $thisuser['privacykey'] . '">';

		//Block 6: Sichtbarkeit
		$blPrivacy = "<h3>Sichtbarkeitseinstellungen</h3>Welche Einstellungen sollen Besuchern deines Profils angezeigt werden?";



		$form_bottom = '<p /><p /><input type=submit value="Änderungen speichern"><input type="hidden" name="action" value="save"></form>';

	} else {
		//Zeige das Profil ohne Änderungsmöglichkeit an

		//Ggf. Speichern / Verwerfen der geänderten Werte
		if(isset($_POST['action']) && ($_POST['action'] == 'save')) 
		{
			// Nutzerdaten überschreiben:
			$thisuser['birthday'] = $_POST['txtGeburtstag'];
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
			$thisuser['postalcode'] = $_POST['txtPostleitzahl'];

			//Temporär:
			$thisuser['privacykey'] = $_POST['tmpPrivacy'];
			
			//Änderungen speichern
			update_user($thisuser);
			header("Refresh:0");
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
	echo '<div align="center">' . $blAdresse;
	//Funktioniert noch nicht :
	if ($shStrasse && $shHausnummer && $shPlzOrt) createMap($thisuser['place'] . ',' . $thisuser['street'] . ',' . $thisuser['housenumber']);
	echo '</div>';
	echo '<p />';
	echo '<div align="center">' . $blKontakt . '</div>';
?>

<!--Ende des Formulars zum Ändern der Profileinstellungen:-->
<?php echo $form_bottom?>

<?php require "./includes/_bottom.php"; ?>

