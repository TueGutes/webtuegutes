<?php
/*
*@Autor Christian Hock, Klaus Sobotta, Nick Nolting (refactored Henrik Huckauf)
enthält Teile von deeds_create und deeds_bearbeiten
*/
require './includes/DEF.php';
require './includes/UTILS.php';
require './includes/db_connector.php';
require './includes/_top.php';

//Zeigt an auf welcher Unterseite man gerade ist.
//Initialisiert alle Variablen bei Erststart
if(!isset($_SESSION['Seite']))$_SESSION['Seite'] =0;
if($_SESSION['Seite']==0){
$_SESSION['tat_name'] ='';
$_SESSION['tat_pictures'] ='';
$_SESSION['tat_description'] ='';
$_SESSION['tat_category'] ='';
$_SESSION['tat_street'] ='';
$_SESSION['tat_housenumber'] ='';
$_SESSION['tat_postalcode'] ='';
$_SESSION['tat_place'] ='';
$_SESSION['tat_starttime'] ='';
$_SESSION['tat_endtime'] ='';
$_SESSION['tat_organization'] ='';
$_SESSION['tat_countHelper'] ='1';
$_SESSION['tat_idTrust'] ='1';
date_default_timezone_set("Europe/Berlin");
$_SESSION['Seite'] +=1;
}

//Damit nur eine Seite aufgerufen wird
$stop='0';
//zurückbuttonvariable
if(isset($_POST['button'])){
$button=$_POST['button'];
if($_POST['button']=='weiter')$_SESSION['Seite']+=1;
if($_POST['button']=='zurück')$_SESSION['Seite']-=1;
}
if(isset($_POST['Seite']))$_SESSION['Seite']=$_POST['Seite'];


//Name setzen
if($_SESSION['Seite'] ==1 || $_SESSION['Seite'] ==2){
//Guckt ob der Aufruf für Seite 2 erfolgreich war
if($_SESSION['Seite'] ==2) {
	if(isset($_POST['name']))$_SESSION['tat_name']=$_POST['name'];
	if(DBFunctions::db_doesGuteTatNameExists($_SESSION['tat_name'])){
		$_SESSION['Seite'] =1;
		$stop=1;
	}else if ($_SESSION['tat_name'] === ''){
		$stop=2;
		$_SESSION['Seite'] =1;
	}
}
if($_SESSION['Seite'] ==1 ||$_SESSION['Seite'] ==3){

echo'
<h2>Wähle zuerst einen aussagekräftigen Namen für deine Tat. :)</h2>
<h3>Jede Tat hat einen eigenen Namen und kann auch durch diesen gesucht werden. Deine Tat wird dem entsprechend öfter aufgerufen, wenn dein Name intuitiv verständlich ist.</h3>
<br><br>';
//Fehlermeldung für nicht erfolgreichen Aufruf.
if($stop=='1')echo '<h3><red>Eine andere Tat ist bereits unter diesem Namen veröffentlicht.</red></h3>';
if($stop=='2')echo '<h3><red>Bitte einen neuen Namen eingeben.</red></h3><br>';
//le buttons
echo'
<br><br>
	<form action="" method="POST">
		<br>
		<br>
		<input type="text" name="name" value="';echo $_SESSION['tat_name'] ;echo'" placeholder="Name der Tat" />
		<br>
		<br>
		<input type="submit" name="button" value="weiter" />
</form>	';
}
}
//Bild setzen
if($_SESSION['Seite'] ==2 || $_SESSION['Seite']==3){
	if(isset($_FILES['pictures']))$_SESSION['tat_pictures']=$_FILES['pictures'];	
	if($_SESSION['Seite']==2){
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
		if($_SESSION['tat_pictures']!=''){
			if(isset($_FILES['pictures']))$_SESSION['tat_pictures']='data: ' . mime_content_type($_FILES['pictures']['tmp_name']) . ';base64,' . base64_encode (file_get_contents($_FILES['pictures']['tmp_name']));
			echo' <center><img src="';echo $_SESSION['tat_pictures'];echo'" ></center>
				<br>
				<br>
				<br>';
		}
		else if(isset($_FILES['pictures'])){
		$_SESSION['tat_pictures']='data: ' . mime_content_type($_FILES['pictures']['tmp_name']) . ';base64,' . base64_encode (file_get_contents($_FILES['pictures']['tmp_name'])); 
		echo '<h3><green>Das Bild ist nun hochgeladen</h2></green>
				<center><img src="';echo $_SESSION['tat_pictures'];echo'" ></center>
				<br>
				<br>
				<br>';
		}
		
		echo'
		</form>
		<form action="" method="POST" enctype="multipart/form-data">		
		';
		if(isset($_FILES['pictures'])){
			echo'<input type="hidden" name="pictures" value="';$_SESSION['tat_pictures'] ;
		echo'" />';
		}
		echo'
		<input type="submit" name="button" value="zurück" />
		<input type="submit" name="button" value="weiter" />
</form>	';
}
}
//Beschreibung setzen

if(($_SESSION['Seite'] ==3 || $_SESSION['Seite'] ==4)){	
if($_SESSION['Seite'] ==4){
	if(isset($_POST['description']))$_SESSION['tat_description']=$_POST['description'];
	if ($_SESSION['tat_description'] === ''){
		$stop=1;
		$_SESSION['Seite'] =3;
		
}
}
if($_SESSION['Seite'] ==3){
	
echo'
<h2>Beschreibe deine Tat.</h2>
<h3>Was und wie ist es zu tun? </h3>';
if($stop==1)echo '<h3><red>Bitte beschreibe deine Tat.</red></h3><br>';
echo '
<br><br>
<div class="center block deeds_create">
		<form action="" method="POST" enctype="multipart/form-data">
		<br>
		<br>
		<textarea id="text" name="description" rows="10" placeholder="Beschreiben Sie die auszuführende Tat. Werben Sie für Ihr Angebot. Nutzen sie ggf. eine Rechtschreibüberprüfung." >';echo $_SESSION['tat_description'] ;echo'</textarea>
		<br><br>
		<br>y
		<input type="submit" name="button" value="zurück" />
		<input type="submit" name="button" value="weiter" />
</form>	';
} 
}
if(($_SESSION['Seite'] ==4 ||$_SESSION['Seite'] ==5)){

	if($_SESSION['Seite'] ==5){
	if(isset($_POST['street']))$_SESSION['tat_street']=$_POST['street'];
	if(isset($_POST['housenumber']))$_SESSION['tat_housenumber']=$_POST['housenumber'];
	if(isset($_POST['postalcode']))$_SESSION['tat_postalcode']=$_POST['postalcode'];
	if(isset($_POST['place']))$_SESSION['tat_place']=$_POST['place'];
	if(isset($_POST['organization']))$_SESSION['tat_organization']=$_POST['organization'];
	if(isset($_POST['starttime']))$_SESSION['tat_starttime']=$_POST['starttime'];
	if(isset($_POST['endtime']))$_SESSION['tat_endtime']=$_POST['endtime'];
	if(isset($_POST['idTrust']))$_SESSION['tat_idTrust']=$_POST['idTrust'];
	if(isset($_POST['countHelper']))$_SESSION['tat_countHelper']=$_POST['countHelper'];
	
	if(!isset($_POST['street'])){
		$stop=1;
	}else if($_POST['housenumber']==''){
		$stop=2;
	}else if($_POST['postalcode']==''|| !is_numeric($_POST['postalcode'])){
		$stop=3;
	}else if($_POST['place']==''){
		$stop=4;
	}else if($_POST['organization']==''){
		$stop=5;
	}else if($_POST['starttime']==''){
		//|| !DateHandler::isValid($_GET['starttime']) braucht es mit dem Kalender nicht mehr 
		$stop=6;
	}else if($_POST['endtime']=='' ){
		//|| !DateHandler::isValid($_GET['endtime'])
		$stop=7;
	}else if ((DBFunctions::db_getIdPostalbyPostalcodePlace($_POST['postalcode'],$_POST['place'])==false)){
		$stop=8;
	}
	}	
if($stop!=0)$_SESSION['Seite'] =4;
if($_SESSION['Seite'] ==4){
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
<form action="" method="POST" enctype="multipart/form-data">
		<br>
		<br>
		<br>
		<input type="text" name="street" value="';echo $_SESSION['tat_street'] ;echo'" placeholder="Straßenname" />
		<input type="text" name="housenumber" value="'; echo $_SESSION['tat_housenumber'] ;echo '" placeholder="Hausnummer" />
		<br>
		<input type="text" name="postalcode" value="'; echo $_SESSION['tat_postalcode'] ;echo '" placeholder="Postleitzahl" />
		<br>
		<input type="text" name="place" value="'; echo $_SESSION['tat_place'] ; echo'" placeholder="Stadtteil" />
		<br>
		<input type="text" name="starttime" value="'; echo $_SESSION['tat_starttime']; echo'" placeholder="Startzeitpunkt (dd.mm.yyyy HH:MM)" />
		<br>
		<input type="text" name="endtime" value="'; echo $_SESSION['tat_endtime']; echo'" placeholder="Endzeitpunkt (dd.mm.yyyy HH:MM)" />
		<br>
		<input type="text" name="organization" value="'; echo $_SESSION['tat_organization']; echo'" placeholder="Organisation" />
		<br>
		Benötigte Helfer:<br>
		<input type="number" name="countHelper" value="'; echo $_SESSION['tat_countHelper'];echo'" placeholder="Benötigte Helfer" />
		<br>
		Erforderlicher Verantwortungslevel:<br>
		<select name="idTrust">
			<option value="1"<?php echo $_SESSION[';echo"'";echo"tat_idTrust']";echo' == 1?" selected":""; >1</option>
			<option value="2"<?php echo $_SESSION[';echo"'";echo"tat_idTrust']";echo' == 2?" selected":""; >2</option>
			<option value="3"<?php echo $_SESSION[';echo"'";echo"tat_idTrust']";echo' == 3?" selected":""; >3</option>
		</select>
		<br><br>
		<br><br>
		<br>
		<input type="submit" name="button" value="zurück" />
		<input type="submit" name="button" value="weiter" />
</form>	';
}
}
if($_SESSION['Seite'] ==5){	
		//Einfügen der Guten Tat
		$uid = DBFunctions::db_idOfBenutzername($_USER->getUsername());
		$plz = DBFunctions::db_getIdPostalbyPostalcodePlace($_SESSION['tat_postalcode'], $_SESSION['tat_place']);
		$start_dh = (new DateHandler())->set($_SESSION['tat_starttime']);
		$category='temp';
		$end_dh = (new DateHandler())->set($_SESSION['tat_endtime']);
		DBFunctions::db_createGuteTat($_SESSION['tat_name'], $uid, $category, $_SESSION['tat_street'], $_SESSION['tat_housenumber'], 
									  $plz, $start_dh->get(),$end_dh->get(), $_SESSION['tat_organization'], $_SESSION['tat_countHelper'],
									  $_SESSION['tat_idTrust'], $_SESSION['tat_description'], $_SESSION['tat_pictures']);
		
		//Versenden der Info-Mails
		
		//Bestimmen der Empfänger
		$mods = DBFunctions::db_getAllModerators();
		$admins = DBFunctions::db_getAllAdministrators();

		//Festlegen des Mail-Inhalts
		$mailSubject = 'Gute Tat ' . "'" . $_SESSION['tat_name'] . "'" . ' wurde erstellt!';
		$mailContent1 = '<div style="margin-left:10%;margin-right:10%;background-color:#757575"><img src="img/wLogo.png" alt="TueGutes" title="TueGutes" style="width:25%"/></div><h2>Hallo!';
		$mailContent2 = '</h2><br>' . $_USER->getUsername() . ' hat gerade eine neue gute Tat erstellt. <br>Um die gute Tat zu bestätigen oder abzulehnen, klicke auf den folgenden Link: <br><a href="' . $HOST . '/deeds_details?id=' . DBFunctions::db_getIDOfGuteTatByName($_SESSION['tat_name']) . '">Zur guten Tat</a>';

		//Versenden der Emails an Moderatoren
		for ($i = 0; $i < sizeof($mods); $i++) {
			sendEmail($mods[$i], $mailSubject, $mailContent1 . $mailContent2);
		}
		//Versenden der Emails an Administratoren
		for ($i = 0; $i < sizeof($admins); $i++) {
			sendEmail($admins[$i], $mailSubject, $mailContent1 . $mailContent2);
			
		}
	
	
	$_SESSION['Seite']='0';
echo'
<h2><green>Deine Tat wurde erstellt! </green></h2>
<h3>und wird nun von uns geprüft. </h3>
<a href="./deeds.php"><input type="button" name="Toll" value="Toll!"/></a>';
}
require './includes/_bottom.php';
?>