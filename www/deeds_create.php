<?php
/*
*@Autor Christian Hock, Klaus Sobotta, Nick Nolting (refactored Henrik Huckauf)
enthält Teile von deeds_create und deeds_bearbeiten
*/

require './includes/DEF.php';
require './includes/UTILS.php';
require './includes/db_connector.php';

require './includes/_top.php';
	
$pictures = isset($POST['pictures']) ? $_POST['pictures'] : '';
$name = isset($_GET['name']) ? $_GET['name'] : '';
$description = isset($_GET['description']) ? $_GET['description'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$street = isset($_GET['street']) ? $_GET['street'] : '';
$housenumber = isset($_GET['housenumber']) ? $_GET['housenumber'] : '';
$postalcode = isset($_GET['postalcode']) ? $_GET['postalcode'] : '';
$place = isset($_GET['place']) ? $_GET['place'] : '';
$startdate = isset($_GET['startdate']) ? $_GET['startdate'] . ' ' . $_GET['starttime_hours'] . ':' . $_GET['starttime_minutes'] : '';
$enddate = isset($_GET['enddate']) ? $_GET['enddate'] . ' ' . $_GET['endtime_hours'] . ':' . $_GET['endtime_minutes'] : '';
$organization = isset($_GET['organization']) ? $_GET['organization'] : '';
$countHelper = isset($_GET['countHelper']) ? $_GET['countHelper'] : '1';
$idTrust = isset($_GET['tat_verantwortungslevel']) ? $_GET['tat_verantwortungslevel'] : '';

//Zeigt an auf welcher Unterseite man gerade ist.
isset($_GET['Seite']) ? $_GET['Seite'] : '';
if(isset($_GET['Seite'])){
	$Seite = $_GET['Seite'];
	}else{
$Seite =1;
	}
//Damit nur eine Seite aufgerufen wird
$stop='0';
//zurückbuttonvariable
if(isset($_GET['button'])){
$button=$_GET['button'];}


//Name setzen
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
	<h2>Wähle zuerst einen aussagekräftigen Namen für deine Tat. :)</h2>
	<h3>Jede Tat hat einen eigenen Namen und kann auch durch diesen gesucht werden. Deine Tat wird dem entsprechend öfter aufgerufen, wenn dein Name intuitiv verständlich ist.</h3>;
	<br><br>';
	//Fehlermeldung für nicht erfolgreichen Aufruf.
	if($stop=='1')echo '<h3><red>Eine andere Tat ist bereits unter diesem Namen veröffentlicht.</red></h3>';
	if($stop=='2')echo '<h3><red>Bitte einen neuen Namen eingeben.</red></h3><br>';
	//le buttons
	echo'
	<br><br>
		<form action="" method="GET">
			<br>
			<br>
			<input type="text" name="name" value="';echo $name ;echo'" placeholder="Name der Tat" required />
			<br>
			<br>
			<input type="hidden" name="Seite" value="';echo 2 ; echo'"required />
			<input type="submit" name="button" value="weiter" />
	</form>	';
	}
}
//Bild setzen
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
			if(isset($_FILES['pictures'])){
			$pictures='data: ' . mime_content_type($_FILES['pictures']['tmp_name']) . ';base64,' . base64_encode (file_get_contents($_FILES['pictures']['tmp_name'])); 
			echo '<h3><green>Das Bild ist nun hochgeladen</h2></green>
					<center><img src="';echo $pictures;echo'" ></center>
					<br>
					<br>
					<br>';
			}
			
			echo'
			</form>
			<form action="" method="GET" enctype="multipart/form-data">	
			<input type="hidden" name="Seite" value="3"required />		
			<input type="hidden" name="name" value="';echo $name ;echo'"required />
			<input type="hidden" name="pictures" value="';echo $pictures ;echo'"required />
			<input type="submit" name="button" value="zurück" />
			<input type="submit" name="button" value="weiter" />
	</form>	';
}
//Beschreibung setzen

if((($Seite==3 || $Seite==4)&& $button=='weiter' )||(($Seite==5)&& $button=='zurück')){	
	if($Seite==4){
		$data=$_GET['description'];
		if ($data === ''){
			$stop=1;
			$Seite=3;
		}
	}
	if($Seite==3 || $Seite==5){
		if($stop==1)echo '<h3><red>Bitte eine neue Beschreibung eingeben.</red></h3><br>';
		echo'
		<h2>Beschreibe deine Tat.</h2>
		<h3>Was und wie ist es zu tun? </h3>
		<br><br>
		<div class="center block deeds_create">
				<form action="" method="GET" enctype="multipart/form-data">
				<br>
				<br>
				<textarea id="text" name="description" rows="10" placeholder="Beschreiben Sie die auszuführende Tat. Werben Sie für Ihr Angebot. Nutzen sie ggf. eine Rechtschreibüberprüfung." required>';echo $description ;echo'</textarea>
				<br><br>
				<br>

				
				<input type="hidden" name="Seite" value="4"required />
				<input type="hidden" name="name" value="';echo $name ;echo'"required />
				<input type="hidden" name="pictures" value="';echo $pictures ;echo'"required />
				<input type="submit" name="button" value="zurück" />
				<input type="submit" name="button" value="weiter" />
		</form>	';
	} 
}
if(($Seite==4 ||$Seite==5)&& $button=='weiter' ){

	if($Seite==5){
		if(!isset($_GET['street'])){
			$stop=1;
		}else if(!isset($_GET['housenumber'])){
			$stop=2;
		}else if(!isset($_GET['postalcode'])|| !is_numeric($_GET['postalcode'])){
			$stop=3;
		}else if(!isset($_GET['place'])){
			$stop=4;
		}else if(!isset($_GET['organization'])){
			$stop=5;
		}else if(!isset($_GET['startdate']) ||
			(intval($_GET['starttime_minutes']) % 5 != 0 && intval($_GET['starttime_minutes']) != 0) ||
			!DateHandler::isValid($_GET['startdate'] . ' ' . $_GET['starttime_hours'] . ':' . $_GET['starttime_minutes'], 'd.m.Y H:i')){
			$stop=6;
		}else if(!isset($_GET['enddate']) ||
			(intval($_GET['endtime_minutes']) % 5 != 0 && intval($_GET['endtime_minutes']) != 0) ||
			!DateHandler::isValid($_GET['enddate'] . ' ' . $_GET['endtime_hours'] . ':' . $_GET['endtime_minutes'], 'd.m.Y H:i')){
			$stop=7;
		}else if ((DBFunctions::db_getIdPostalbyPostalcodePlace($postalcode,$place)==false)){
			$stop=8;
		}
	}	
	if($stop!=0)$Seite=4;
	if($Seite==4){
		echo'
		<h2>Rahmendaten</h2>
		<h3>Hier noch einmal alle notwendigen Informationen für Bewerber.</h3>';
			if($stop==1)echo '<h3><red>Bitte eine neue Straße eingeben.</red></h3><br>';
			if($stop==2)echo '<h3><red>Bitte eine neue Hausnummer eingeben.</red></h3><br>';	
			if($stop==3)echo '<h3><red>Die Postleitzahl bitte als Zahl eingeben.</red></h3><br>';	
			if($stop==4)echo '<h3><red>Bitte einen Ort angeben.</red></h3><br>';
			if($stop==5)echo '<h3><red>Bitte eine neue Organisation eingeben.</red></h3><br>';
			if($stop==6)echo '<h3><red>Das Format von der Startzeit ist falsch.</red></h3><br>';
			if($stop==7)echo '<h3><red>Das Format von der Endzeit ist falsch.</red></h3><br>';
			if($stop==8)echo '<h3><red>Die Postleitzahl passt nicht zum Ort.</red></h3><br>';	
			
			echo '<br><br>
		<div class="center block deeds_create">
		<form action="" method="GET" enctype="multipart/form-data">
				<br>
				<br>
				<br>
				<input type="text" name="street" value="';echo $street ;echo'" placeholder="Straßenname" required />
				<input type="text" name="housenumber" value="'; echo $housenumber ;echo '" placeholder="Hausnummer" required />
				<br>
				<input type="text" name="postalcode" value="'; echo $postalcode ;echo '" placeholder="Postleitzahl" required />
				<br>
				<input type="text" name="place" value="'; echo $place ; echo'" placeholder="Stadtteil" required />
				<br>';
				
				/*
				var o = '<select>';
				for(var i = 0; i < 60; i++)
				{
					var p = i < 10 ? '0' + i : i;
					o += '<option value="' + p + '">' + p + '</option>';
				}
				o += '</select>';
				document.getElementById('out').innerHTML = o;
				
				
				
				var o = '<option value="00">00</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option>';
				o = o.replaceAll('</option>', '</option');
				var parts = o.split('>');
				for(var i = 0; i < parts.length -1; i++)
				{
					var curNumber = parseInt(parts[i].split('"')[1]);
					parts[i] = parts[i] + "' . (intval($start_dh->getHours()) == " + curNumber + " ? ' selected' : '') . '";
				}
				var output = parts.join(">");
				output.replaceAll('</option', '</option>');
				document.getElementById('out').innerHTML = output;
				*/
				$start_dh = (new DateHandler())->set($startdate);
				$end_dh = (new DateHandler())->set($enddate);
				echo 'Beginn:<br><input type="date" name="startdate" value="' . ($start_dh !== false ? $start_dh->get('d.m.Y') : '') . '" placeholder="DD.MM.YYYY" required />';
				if($start_dh === false) $start_dh = (new DateHandler())->setHours(8)->setMinutes(0);
				echo ' um <select name="starttime_hours"><option value="00"' . (intval($start_dh->getHours()) == 0 ? ' selected' : '') . '>00</option><option value="01"' . (intval($start_dh->getHours()) == 1 ? ' selected' : '') . '>01</option><option value="02"' . (intval($start_dh->getHours()) == 2 ? ' selected' : '') . '>02</option><option value="03"' . (intval($start_dh->getHours()) == 3 ? ' selected' : '') . '>03</option><option value="04"' . (intval($start_dh->getHours()) == 4 ? ' selected' : '') . '>04</option><option value="05"' . (intval($start_dh->getHours()) == 5 ? ' selected' : '') . '>05</option><option value="06"' . (intval($start_dh->getHours()) == 6 ? ' selected' : '') . '>06</option><option value="07"' . (intval($start_dh->getHours()) == 7 ? ' selected' : '') . '>07</option><option value="08"' . (intval($start_dh->getHours()) == 8 ? ' selected' : '') . '>08</option><option value="09"' . (intval($start_dh->getHours()) == 9 ? ' selected' : '') . '>09</option><option value="10"' . (intval($start_dh->getHours()) == 10 ? ' selected' : '') . '>10</option><option value="11"' . (intval($start_dh->getHours()) == 11 ? ' selected' : '') . '>11</option><option value="12"' . (intval($start_dh->getHours()) == 12 ? ' selected' : '') . '>12</option><option value="13"' . (intval($start_dh->getHours()) == 13 ? ' selected' : '') . '>13</option><option value="14"' . (intval($start_dh->getHours()) == 14 ? ' selected' : '') . '>14</option><option value="15"' . (intval($start_dh->getHours()) == 15 ? ' selected' : '') . '>15</option><option value="16"' . (intval($start_dh->getHours()) == 16 ? ' selected' : '') . '>16</option><option value="17"' . (intval($start_dh->getHours()) == 17 ? ' selected' : '') . '>17</option><option value="18"' . (intval($start_dh->getHours()) == 18 ? ' selected' : '') . '>18</option><option value="19"' . (intval($start_dh->getHours()) == 19 ? ' selected' : '') . '>19</option><option value="20"' . (intval($start_dh->getHours()) == 20 ? ' selected' : '') . '>20</option><option value="21"' . (intval($start_dh->getHours()) == 21 ? ' selected' : '') . '>21</option><option value="22"' . (intval($start_dh->getHours()) == 22 ? ' selected' : '') . '>22</option><option value="23"' . (intval($start_dh->getHours()) == 23 ? ' selected' : '') . '>23</option></select>';
				echo ' : <select name="starttime_minutes"><option value="00"' . (intval($start_dh->getMinutes()) == 0 ? ' selected' : '') . '>00</option><option value="05"' . (intval($start_dh->getMinutes()) == 5 ? ' selected' : '') . '>05</option><option value="10"' . (intval($start_dh->getMinutes()) == 10 ? ' selected' : '') . '>10</option><option value="15"' . (intval($start_dh->getMinutes()) == 15 ? ' selected' : '') . '>15</option><option value="20"' . (intval($start_dh->getMinutes()) == 20 ? ' selected' : '') . '>20</option><option value="25"' . (intval($start_dh->getMinutes()) == 25 ? ' selected' : '') . '>25</option><option value="30"' . (intval($start_dh->getMinutes()) == 30 ? ' selected' : '') . '>30</option><option value="35"' . (intval($start_dh->getMinutes()) == 35 ? ' selected' : '') . '>35</option><option value="40"' . (intval($start_dh->getMinutes()) == 40 ? ' selected' : '') . '>40</option><option value="45"' . (intval($start_dh->getMinutes()) == 45 ? ' selected' : '') . '>45</option><option value="50"' . (intval($start_dh->getMinutes()) == 50 ? ' selected' : '') . '>50</option><option value="55"' . (intval($start_dh->getMinutes()) == 55 ? ' selected' : '') . '>55</option></select>';
				echo ' Uhr<br><br>';
				
				echo 'Ende:<br><input type="date" name="enddate" value="' . ($end_dh !== false ? $end_dh->get('d.m.Y') : '') . '" placeholder="DD.MM.YYYY" required />';
				if($end_dh === false) $end_dh = (new DateHandler())->setHours(8)->setMinutes(0);
				echo ' um <select name="endtime_hours"><option value="00"' . (intval($end_dh->getHours()) == 0 ? ' selected' : '') . '>00</option><option value="01"' . (intval($end_dh->getHours()) == 1 ? ' selected' : '') . '>01</option><option value="02"' . (intval($end_dh->getHours()) == 2 ? ' selected' : '') . '>02</option><option value="03"' . (intval($end_dh->getHours()) == 3 ? ' selected' : '') . '>03</option><option value="04"' . (intval($end_dh->getHours()) == 4 ? ' selected' : '') . '>04</option><option value="05"' . (intval($end_dh->getHours()) == 5 ? ' selected' : '') . '>05</option><option value="06"' . (intval($end_dh->getHours()) == 6 ? ' selected' : '') . '>06</option><option value="07"' . (intval($end_dh->getHours()) == 7 ? ' selected' : '') . '>07</option><option value="08"' . (intval($end_dh->getHours()) == 8 ? ' selected' : '') . '>08</option><option value="09"' . (intval($end_dh->getHours()) == 9 ? ' selected' : '') . '>09</option><option value="10"' . (intval($end_dh->getHours()) == 10 ? ' selected' : '') . '>10</option><option value="11"' . (intval($end_dh->getHours()) == 11 ? ' selected' : '') . '>11</option><option value="12"' . (intval($end_dh->getHours()) == 12 ? ' selected' : '') . '>12</option><option value="13"' . (intval($end_dh->getHours()) == 13 ? ' selected' : '') . '>13</option><option value="14"' . (intval($end_dh->getHours()) == 14 ? ' selected' : '') . '>14</option><option value="15"' . (intval($end_dh->getHours()) == 15 ? ' selected' : '') . '>15</option><option value="16"' . (intval($end_dh->getHours()) == 16 ? ' selected' : '') . '>16</option><option value="17"' . (intval($end_dh->getHours()) == 17 ? ' selected' : '') . '>17</option><option value="18"' . (intval($end_dh->getHours()) == 18 ? ' selected' : '') . '>18</option><option value="19"' . (intval($end_dh->getHours()) == 19 ? ' selected' : '') . '>19</option><option value="20"' . (intval($end_dh->getHours()) == 20 ? ' selected' : '') . '>20</option><option value="21"' . (intval($end_dh->getHours()) == 21 ? ' selected' : '') . '>21</option><option value="22"' . (intval($end_dh->getHours()) == 22 ? ' selected' : '') . '>22</option><option value="23"' . (intval($end_dh->getHours()) == 23 ? ' selected' : '') . '>23</option></select>';
				echo ' : <select name="endtime_minutes"><option value="00"' . (intval($end_dh->getMinutes()) == 0 ? ' selected' : '') . '>00</option><option value="05"' . (intval($end_dh->getMinutes()) == 5 ? ' selected' : '') . '>05</option><option value="10"' . (intval($end_dh->getMinutes()) == 10 ? ' selected' : '') . '>10</option><option value="15"' . (intval($end_dh->getMinutes()) == 15 ? ' selected' : '') . '>15</option><option value="20"' . (intval($end_dh->getMinutes()) == 20 ? ' selected' : '') . '>20</option><option value="25"' . (intval($end_dh->getMinutes()) == 25 ? ' selected' : '') . '>25</option><option value="30"' . (intval($end_dh->getMinutes()) == 30 ? ' selected' : '') . '>30</option><option value="35"' . (intval($end_dh->getMinutes()) == 35 ? ' selected' : '') . '>35</option><option value="40"' . (intval($end_dh->getMinutes()) == 40 ? ' selected' : '') . '>40</option><option value="45"' . (intval($end_dh->getMinutes()) == 45 ? ' selected' : '') . '>45</option><option value="50"' . (intval($end_dh->getMinutes()) == 50 ? ' selected' : '') . '>50</option><option value="55"' . (intval($end_dh->getMinutes()) == 55 ? ' selected' : '') . '>55</option></select>';
				echo ' Uhr<br>';
				
				echo '<input type="text" name="organization" value="'; echo $organization; echo'" placeholder="Organisation" />
				<br>
				Benötigte Helfer:<br>
				<input type="number" name="countHelper" value="'; echo $countHelper;echo'" min="0" placeholder="Benötigte Helfer" required />
				<br>
				Erforderlicher Verantwortungslevel:<br>
				<select name="tat_verantwortungslevel">
					<option value="1"<?php echo $idTrust == 1?" selected":""; >1</option>
					<option value="2"<?php echo $idTrust == 2?" selected":""; >2</option>
					<option value="3"<?php echo $idTrust == 3?" selected":""; >3</option>
				</select>
				<br><br>
				<br><br>
				<br>
				<input type="hidden" name="name" value="';echo $name ;echo'"required />
				<input type="hidden" name="pictures" value="';echo $pictures ;echo'"required />
				<input type="hidden" name="description" value="';echo $description ;echo'"required />
				<input type="hidden" name="Seite" value="5"required />
				<input type="submit" name="button" value="zurück" />
				<input type="submit" name="button" value="weiter" />
		</form>	';
	}
}
if($Seite==5&&($button!='zurück')){
	$error = false;
	//Name der guten Tat
	if(empty($name) || DBFunctions::db_doesGuteTatNameExists($name))
	{
		$error = true;
	}

	//Falls eine fehlerhafte PLZ angegeben wird
	if(!is_numeric($postalcode))
	{
		$error = true;
	}

	//TODO Enddatum darf nicht vor dem Startdatum liegen
	
	//Startzeitpunkt
	$start_dh = (new DateHandler())->set($startdate);
	if(!$start_dh)
	{
		$error = true;
	}
	//Endzeitpunkt
	$end_dh = (new DateHandler())->set($enddate);
	if(!$end_dh)
	{
		$error = true;
	}

	if(!DBFunctions::db_getIdPostalbyPostalcodePlace($postalcode, $place))
	{
		$error = true;
	}

	//Anzahl Helfer keine Zahl
	if(!is_numeric($countHelper))
	{
		$error = true;
	}

	if(!$error)
	{
		
		//Einfügen der Guten Tat
		$uid = DBFunctions::db_idOfBenutzername($_USER->getUsername());
		$plz = DBFunctions::db_getIdPostalbyPostalcodePlace($postalcode, $place);

		// TIMM HIER: Temporäre Fix damit amn gute Taten erstellen kann.
		$category = 'keine Angabe';
		//Bugfix zu ende
		DBFunctions::db_createGuteTat($name, $uid, $category, $street, $housenumber, 
									  $plz, $start_dh->get(),$end_dh->get(), $organization, $countHelper,
									  $idTrust, $description, $pictures);
		
		//Versenden der Info-Mails
		
		//Bestimmen der Empfänger
		$mods = DBFunctions::db_getAllModerators();
		$admins = DBFunctions::db_getAllAdministrators();

		//Festlegen des Mail-Inhalts
		$mailSubject = 'Gute Tat ' . "'" . $_GET['name'] . "'" . ' wurde erstellt!';
		$mailContent1 = '<div style="margin-left:10%;margin-right:10%;background-color:#757575"><img src="img/wLogo.png" alt="TueGutes" title="TueGutes" style="width:25%"/></div><h2>Hallo!';
		$mailContent2 = '</h2><br>' . $_USER->getUsername() . ' hat gerade eine neue gute Tat erstellt. <br>Um die gute Tat zu bestätigen oder abzulehnen, klicke auf den folgenden Link: <br><a href="' . $HOST . '/deeds_details?id=' . DBFunctions::db_getIDOfGuteTatByName($_GET['name']) . '">Zur guten Tat</a>';

		//Versenden der Emails an Moderatoren
		for ($i = 0; $i < sizeof($mods); $i++) {
			sendEmail($mods[$i], $mailSubject, $mailContent1 . $mailContent2);
		}
		//Versenden der Emails an Administratoren
		for ($i = 0; $i < sizeof($admins); $i++) {
			sendEmail($admins[$i], $mailSubject, $mailContent1 . $mailContent2);
		}
	}
	echo'
	<h2><green>Deine Tat wurde erstellt! </green></h2>
	<h3>und wird nun von uns geprüft. </h3>
	<a href="./deeds.php"><input type="button" name="Toll" value="Toll!"/></a>';
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
$startdate = isset($_POST['startdate']) ? $_POST['startdate'] : '';
$enddate = isset($_POST['enddate']) ? $_POST['enddate'] : '';
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
	$start_dh = (new DateHandler())->set($startdate);
	if(!$start_dh)
	{
		$output .= '<red>Es wurde kein gültiger Startzeitpunkt für die gute Tat festgelegt.</li>';
		$error = true;
	}
	//Endzeitpunkt
	$end_dh = (new DateHandler())->set($enddate);
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
		$startdate = '';
		$enddate = '';
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
		<input type="text" name="startdate" value="<?php echo $startdate; ?>" placeholder="Startzeitpunkt (dd.mm.yyyy HH:MM)" required />
		<br>
		<input type="text" name="enddate" value="<?php echo $enddate; ?>" placeholder="Endzeitpunkt (dd.mm.yyyy HH:MM)" required />
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