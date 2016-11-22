<?php
/*
*@author Christian Hock, Klaus Sobotta, Nick Nolting (refactored Henrik Huckauf)
*Verlinkung zu Orten fehlt
*Kategorie soll editierbar sein
*
*DateHandler eingebunden | Henrik Huckauf
*/

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
?>