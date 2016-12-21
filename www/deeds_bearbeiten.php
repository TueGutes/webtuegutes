<?php
/*
*@Autor Christian Hock
<<<<<<< HEAD
* das Bild und den Zeitrahmen zu ändern wurde ausgelassen, da man diese momentan nicht erstellen kann.
* Es wird die Funktion db_fix_plz($plz) genutzt...
=======
>>>>>>> Task3011
*/

require './includes/UTILS.php';
require './includes/DEF.php';
require './includes/ACCESS.php';
require './includes/db_connector.php';
require './includes/_top.php';

//Fehlerüberprüfung auf den Getparameter und prüfung ob die Seite ein zweites mal aufgerufen wurde
if (!isset($_GET['idGuteTat'])&&!isset($_GET['id'])){
echo '<h3>Es ist ein Aufruffehler aufgetreten.<br>Vermutlich haben Sie den Link für diese Seite manuell eingegeben, bzw. herauskopiert und dabei einen Fehler gemacht.<br>Oder die Tat gibt es nicht mehr.<br>Sollte weder noch zutreffen kontaktieren Sie uns bitte.</h3><br>';	
	die();
	}else{
if(isset($_GET['idGuteTat']))$idGuteTat = $_GET['idGuteTat'];
if(isset($_GET['id']))$idGuteTat = $_GET['id'];
	}
//Tat-Objekt holen	für Nutzerüberprüfung
	$tat = DBFunctions::db_getGuteTat($idGuteTat);
	$nutzer =DBFunctions::db_get_user($tat['username']);
	if($nutzer['username']!=$tat['username']){
		die("<h3>unberechtigter Zugriff");
	}

//Prüft welche Tat betroffen ist und fängt Fehler ab 
//Beschreibung
 echo "<h2>Deine Tat bearbeiten </h2>";
 if (isset($_POST['description'])) {
	$data=$_POST['description'];
	if ($data === ''){
		echo '<h3>Bitte eine neue Beschreibung eingeben.</h3><br>';
	}else{
	DBFunctions::db_update_deeds_description($data,$idGuteTat);}}
//Name
   if (isset($_POST['name'])) {
	$data=$_POST['name'];
	if($data==$tat['name']){		
	}else if(DBFunctions::db_doesGuteTatNameExists($data)){
		echo '<h3>Eine andere Tat ist bereits unter diesem Namen veröffentlicht.</h3>';
	}else if ($data === ''){
		echo '<h3>Bitte einen neuen Namen eingeben.</h3><br>';
	}else{
	DBFunctions::db_update_deeds_name($data,$idGuteTat);}}
//Kategorie
    if(isset($_POST['category'])) {
	$data=$_POST['category'];
	DBFunctions::db_update_deeds_category($data,$idGuteTat);}
//Straße
	if(isset($_POST['street'])) {
	$data=$_POST['street'];
	if ($data === ''){
	echo '<h3>Bitte eine neue Straße eingeben.</h3><br>';	
	}else{
	DBFunctions::db_update_deeds_street($data,$idGuteTat);}
	}
//Hausnummer
    if(isset($_POST['housenumber'])) {
	$data=$_POST['housenumber'];
	if ($data === ''){
	echo '<h3>Bitte eine neue Hausnummer eingeben.</h3><br>';	
	}else{
	DBFunctions::db_update_deeds_housenumber($data,$idGuteTat);}
	}		
//Postleitzahl und Ort
	if(isset($_POST['postalcode'])&&(isset($_POST['place']))) {
	$postal=$_POST['postalcode'];
	$place=$_POST['place'];
	if ($postal === ''||$place === ''){
	echo '<h3>Bitte Ort und Plz neu ausfüllen.</h3><br>';	
	}else if (!is_numeric($postal)){
		echo '<h3>Die Postleitzahl bitte als Zahl eingeben.</h3><br>';	
	}else{
	if(DBFunctions::db_getIdPostalbyPostalcodePlace($postal,$place)!=false){
		$data=DBFunctions::db_getIdPostalbyPostalcodePlace($postal,$place);
	DBFunctions::db_update_deeds_postalcode($data,$idGuteTat);
	}else{
		echo '<h3>Bitte eine Adresse in Hannover nehmen.</h3>';
	}
	}
	}
//Organisation
    if(isset($_POST['organization'])) {
	$data=$_POST['organization'];
	if ($data === ''){
	echo '<h3>Bitte eine neue Organisation eingeben.</h3><br>';	
	}else{
	DBFunctions::db_update_deeds_organization($data,$idGuteTat);}	
	}
//Anzahl Helfer
    if(isset($_POST['countHelper'])) {
	$data=$_POST['countHelper'];
	if ($data === ''){
	echo '<h3>Bitte eine neue Anzahl an Helfern eingeben.</h3><br>';	
	}else if (!is_numeric($data)){
		echo '<h3>Die Anzahl der gewünschten Helfer bitte als Zahl eingeben.</h3><br>';	
	}else{
	DBFunctions::db_update_deeds_countHelper($data,$idGuteTat);}
	}
//Verantwortungslevel
  	if(isset($_POST['idTrust'])) {
	$data=$_POST['idTrust'];
	DBFunctions::db_update_deeds_IdTrust($data,$idGuteTat);}
//Zeitrahmen
    if(isset($_POST['von'])) {
		$data=$_POST['von'];
		$data2=$_POST['bis'];
	if (!DateHandler::isValid($data)){
		echo $data;
		echo 'Das Format von der Startzeit ist falsch.';
	}else if (!DateHandler::isValid($data2)){
		echo 'Das Format von der Endzeit ist falsch.';
	}
	else if($data === '' ||$data2 === ''){
	echo 'bitte Start und Endzeit neu eingeben.';
	}else{
		DBFunctions::db_update_deeds_starttime($data,$idGuteTat);
		DBFunctions::db_update_deeds_endtime($data2,$idGuteTat);
		}
	}
else if(isset($_POST['bis'])) {
	echo 'bitte Start und Endzeit neu eingeben.';
	}
//Bild
if(isset($_FILES['picture'])){
	
	$data=$_FILES['picture'];
	if($data==NULL){
	}else{
		//Bei Bedarf aendern
		$bildgroesze = 600*1024; 
		$kb = $bildgroesze/1024;
//falls es mehr Bildformate geben sollte, bitte ergänzen
$bildformate = array('jpeg','jpg', 'gif','png');
$dateiendung = strtolower(pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION));
// Fehler überprüfen
if(!in_array($dateiendung, $bildformate)) {
//zünded auch wenn kein Bild angegeben wurde...wird noch gefixt
 //echo'<h2>Bitte ein Bild hochladen.</h2>';
}else if($_FILES['picture']['size'] > $bildgroesze) {
 echo("Die Maximalgröße beträgt $kb kb.");
}else{																		     
$gleichcodiert='data: ' . mime_content_type($_FILES['picture']['tmp_name']) . ';base64,' . base64_encode (file_get_contents($_FILES['picture']['tmp_name']));
DBFunctions::db_update_deeds_picture($gleichcodiert,$idGuteTat);	
}
}
}
//Tat-Objekt holen -> jetzt damit es akutell ist
	$tat = DBFunctions::db_getGuteTat($idGuteTat);	
//Stringbuilder für den Link und die angezeigen Zeit.
$link = './deeds_bearbeiten?idGuteTat=' . $idGuteTat;
$zeit=$tat['starttime'].'<br> bis <br>'.$tat['endtime'];

?> 

		<center>
		<br>
		<br>
		<form method="post" action="<?php echo $link; ?>" enctype="multipart/form-data">
		<table>
			<tr>
				
				<td><h3>Name der Tat: </td>
				<td><h3><?php echo '<input type="text" name="name" placeholder="Neuer Name" value="'.$tat['name'].'">'?></td>	
			</tr>
			<tr>
			<td><h3>Bild:</h3></td>
			<td></td>	
			</tr>	
			<tr>
			<td colspan="2"><?php echo '<img src="'.$tat["pictures"] .'" >'?><br><input type="file" name="picture" accept="image/*"></td>
			<td></td>	
			</tr>	
			<tr>			
				<td><h3>Beschreibung: </td>
				<td><h3></td>	
			</tr>
			<tr>			
				<td colspan="2"><h3><?php echo '<textarea id="text" type="textarea" cols="65" rows="6" name="description" placeholder="neue Beschreibung">'.$tat['description'].'</textarea>'?></td>	
			</tr>
			<tr>
				<td><h3>Kategorie: </td>
				<td>
				<select name="category" size="1">
				<?php echo'<option ';if($tat['category']==='Altenheim'){echo'selected ';} echo'value="Altenheim">Altenheim</option>';
					  echo'<option ';if($tat['category']==='Busbahnhof'){echo'selected ';} echo'value="Busbahnhof">Busbahnhof</option>';
					  echo'<option ';if($tat['category']==='Müll einsammeln'){echo'selected ';} echo'value="Müll einsammeln">Müll einsammeln</option>';
					?>
				</select></td>
			</tr>
			<tr>
				<td><h3>Straße: </td>
				<td><h3><?php echo '<input type="text" name="street" placeholder="Neue Straße" value="'.$tat['street'].'">'?></td>	
			</tr>
			</tr>
			<tr>
				<td><h3>Hausnummer: </td>
				<td><?php echo '<input type="text" name="housenumber" placeholder="Neue Hausnummer" value="'.$tat['housenumber'].'">'?></td>
			</tr>
			<tr>
				<td><h3>Postleitzahl: </td>
				<td><?php echo '<input type="text" name="postalcode" placeholder="Neue Postleitzahl" value="'.$tat['postalcode'].'">'?></td>
			</tr>
			<tr>
				<td><h3>Ort:</td>
				<td><?php echo '<input type="text" name="place" placeholder="Neuer Ort" value="'.$tat['place'].'">'?></td>
			</tr>				
			<tr>
				<td><br><h3>Zeitrahmen:</td>
				<td><?php echo '<input type="text" name="von" placeholder="Neue Startzeit" value="'.$tat['starttime'].'"><br>bis<br><input type="text" name="bis" placeholder="neue Endzeit" value="'.$tat['endtime'].'">';?>
			</tr>
			<tr>
				<td><h3>Organisation: </td>
				<td><?php echo '<input type="text" name="organization" placeholder="Neue Organisation" value="'.$tat['organization'].'">'?></td>
			</tr>
			<tr>
				<td><h3>Anzahl Helfer: </td>
				<td><?php echo '<input type="text" name="countHelper" placeholder="Neue Anzahl von Helfern" value="'.$tat['countHelper'].'">'?></td>
			</tr>
			<tr>
				<td><h3>Erforderlicher Verantwortungslevel: </td>
				<td>
				<select name="idTrust" size="1">
				<?php echo'<option ';if($tat['idTrust']=='1'){echo'selected ';} echo'value="1">1</option>';
					  echo'<option ';if($tat['idTrust']=='2'){echo'selected ';} echo'value="2">2</option>';
					  echo'<option ';if($tat['idTrust']=='3'){echo'selected ';} echo'value="3">3</option>';
					?>
				</select></td>
			</tr>
		</table>
		<br>
		<br>
		<br>
		<br>
		<br>
		<td><a href="./deeds.php"><input type="button" name="button" value="Abbrechen" /></a></td>
		<td><input type="submit" name="button" value="Änderungen übernehmen"/></td>
		</form>
		</center>
		</form>
<?php require "./includes/_bottom.php";?>