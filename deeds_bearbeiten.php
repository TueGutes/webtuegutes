<?php
/*
*@Autor Christian Hock
* Es wird die Funktion db_fix_plz($plz) genutzt...
*/

require './includes/UTILS.php';
require './includes/DEF.php';
require './includes/ACCESS.php';
require './includes/db_connector.php';
require './includes/_top.php';

//Fehlerüberprüfung auf den Getparameter und prüfung ob die Seite ein zweites mal aufgerufen wurde
if (!isset($_GET['idGuteTat'])){
echo '<h3>Es ist ein Aufruffehler aufgetreten.<br>Vermutlich haben Sie den Link für diese Seite manuell eingegeben, bzw. herauskopiert und dabei einen Fehler gemacht.<br>Oder die Tat gibt es nicht mehr.<br>Sollte weder noch zutreffen kontaktieren Sie uns bitte.</h3><br>';	
	}else{
$idGuteTat = $_GET['idGuteTat'];
	}
//Tat-Objekt holen	für Nutzerüberprüfung
	$tat = db_getGuteTat($idGuteTat);
	$nutzer =db_get_user($tat['username']);
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
	db_update_deeds_description($data,$idGuteTat);}}
//Name
 else if (isset($_POST['name'])) {
	$data=$_POST['name'];
	if(db_doesGuteTatNameExists($data)){
		echo '<h3>Eine andere Tat ist bereits unter diesem Namen veröffentlicht.</h3>';
	}else if ($data === ''){
		echo '<h3>Bitte einen neuen Namen eingeben.</h3><br>';
	}else{
	db_update_deeds_name($data,$idGuteTat);}}
//Kategorie
else if(isset($_POST['category'])) {
	$data=$_POST['category'];
	db_update_deeds_category($data,$idGuteTat);}
//Straße
	else if(isset($_POST['street'])) {
	$data=$_POST['street'];
	if ($data === ''){
	echo '<h3>Bitte eine neue Straße eingeben.</h3><br>';	
	}else{
	db_update_deeds_street($data,$idGuteTat);}
	}
//Hausnummer
else if(isset($_POST['housenumber'])) {
	$data=$_POST['housenumber'];
	if ($data === ''){
	echo '<h3>Bitte eine neue Hausnummer eingeben.</h3><br>';	
	}else{
	db_update_deeds_housenumber($data,$idGuteTat);}
	}		
//Postleitzahl
	else if(isset($_POST['postalcode'])) {
	$data=$_POST['postalcode'];
	if ($data === ''){
	echo '<h3>Bitte eine neue Postleitzahl eingeben.</h3><br>';	
	}else if (!is_numeric($data)){
		echo '<h3>Die Postleitzahl bitte als Zahl eingeben.</h3><br>';	
	}else{
	db_fix_plz($data);
	db_update_deeds_postalcode($data,$idGuteTat);}
	}
//Organisation
else if(isset($_POST['organization'])) {
	$data=$_POST['organization'];
	if ($data === ''){
	echo '<h3>Bitte eine neue Organisation eingeben.</h3><br>';	
	}else{
	db_update_deeds_organization($data,$idGuteTat);}	
	}
//Anzahl Helfer
else if(isset($_POST['countHelper'])) {
	$data=$_POST['countHelper'];
	if ($data === ''){
	echo '<h3>Bitte eine neue Anzahl an Helfern eingeben.</h3><br>';	
	}else if (!is_numeric($data)){
		echo '<h3>Die Anzahl der gewünschten Helfer bitte als Zahl eingeben.</h3><br>';	
	}else{
	db_update_deeds_countHelper($data,$idGuteTat);}
	}
//Verantwortungslevel
else if(isset($_POST['idTrust'])) {
	$data=$_POST['idTrust'];
	db_update_deeds_IdTrust($data,$idGuteTat);}
//Zeitrahmen
else if(isset($_POST['von'])) {
		$data=$_POST['von'];
		$data2=$_POST['bis'];
	if (!DateHandler::isValid($data)){
		echo 'Das Format von der Startzeit ist falsch.';
	}else if (!DateHandler::isValid($data2)){
		echo 'Das Format von der Endzeit ist falsch.';
	}
	else if($data === '' ||$data2 === ''){
	echo 'bitte Start und Endzeit neu eingeben.';
	}else{
		db_update_deeds_starttime($data,$idGuteTat);
		db_update_deeds_endtime($data2,$idGuteTat);
		}
	}
else if(isset($_POST['bis'])) {
	echo 'bitte Start und Endzeit neu eingeben.';
	}
//Bild
else if(isset($_FILES['picture'])){
//Bei Bedarf aendern
$bildgroesze = 600*1024; 
$kb = $bildgroesze/1024;
//falls es mehr Bildformate geben sollte, bitte ergänzen
$bildformate = array('jpeg','jpg', 'gif','png');
$dateiendung = strtolower(pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION));
// Fehler überprüfen
if(!in_array($dateiendung, $bildformate)) {
 echo'<h2>Bitte ein Bild hochladen.';
}else if($_FILES['picture']['size'] > $bildgroesze) {
 echo("Die Maximalgröße beträgt $kb kb.");
}else{																		     
$gleichcodiert='data: ' . mime_content_type($_FILES['picture']['tmp_name']) . ';base64,' . base64_encode (file_get_contents($_FILES['picture']['tmp_name']));
db_update_deeds_picture($gleichcodiert,$idGuteTat);	
}
}
//Tat-Objekt holen -> jetzt damit es akutell ist
	$tat = db_getGuteTat($idGuteTat);	
//Stringbuilder für den Link und die angezeigen Zeit.
$link = './deeds_bearbeiten?idGuteTat=' . $idGuteTat;
$zeit=$tat['starttime'].'<br> bis <br>'.$tat['endtime'];
?> 
		<center>
		<br>
		<br>
		<table>
			<tr>
				<form method="post" action="<?php echo $link; ?>">
				<td><h3>Name der Tat: </td>
				<td><h3><?php echo $tat['name']; ?></td>	
				<td><input type="text" name="name" placeholder="neuer Name"/></td>	
				<td><input type="submit" name="button" value="ändern"/></td>
				</form>
			</tr>				
			<tr>
			
				<form method="post" action="<?php echo $link; ?>" enctype="multipart/form-data">
				<td><h3>Bild: </td>
				<td></td>
				<td><input type="file" name="picture" accept="image/*"></td>
				<td><input type="submit" name="button" value="ändern"/></td>
				</form>
			</tr>	
			<tr>
			<td></td><td><?php echo '<img src="'.$tat["pictures"] .'" >'?></td>	
			</tr>	
			<tr>
				<form method="post" action="<?php echo $link; ?>">
				<td><h3>Beschreibung: </td>
				<td><h3><?php echo $tat['description']; ?></td>	
				<td><input type="text" name="description" placeholder="neue Beschreibung"/></td>
				<td><input type="submit" name="button" value="ändern"/></td>
				</form>
			</tr>
			<tr>
				<form method="post" action="<?php echo $link; ?>">
				<td><h3>Kategorie: </td>
				<td><h3><?php echo $tat['category']; ?></td>	
				<td>
				<select name="category" size="1">
				<option value="Altenheim">Altenheim</option>
				<option value="Busbahnhof">Busbahnhof</option>
				<option value="Müll einsammeln">Müll einsammeln</option>
				</select></td>
				<td><input type="submit" name="button" value="ändern"/></td>
				</form>
			</tr>
			<tr>
				<form method="post" action="<?php echo $link; ?>">
				<td><h3>Straße: </td>
				<td><h3><?php echo $tat['street']; ?></td>	
				<td><input type="text" name="street" placeholder="neue Straße"/></td>
				<td><input type="submit" name="button" value="ändern"/></td>
				</form>
			</tr>
			</tr>
			<tr>
				<form method="post" action="<?php echo $link; ?>">
				<td><h3>Hausnummer: </td>
				<td><h3><?php echo $tat['housenumber']; ?></td>	
				<td><input type="text" name="housenumber" placeholder="neue Straße"/></td>
				<td><input type="submit" name="button" value="ändern"/></td>
				</form>
			</tr>
			<tr>
				<form method="post" action="<?php echo $link; ?>">
				<td><h3>Postleitzahl: </td>
				<td><h3><?php echo $tat['postalcode']; ?></td>	
				<td><input type="text" name="postalcode" placeholder="neue PLZ"/></td>
				<td><input type="submit" name="button" value="ändern"/></td>
				</form>
			</tr>	
			<tr>
				<form method="post" action="<?php echo $link; ?>">
				<td><br><h3>Zeitrahmen:</td>
				<td><h3><?php echo $zeit; ?></td>	
				<td><input type="text" name="von" placeholder="Von"/><br><br><input type="text" name="bis" placeholder="bis"/></td>
				<td><br><br><br><input type="submit" name="button" value="ändern"/></td>
				</form>
			</tr>
			<tr>
				<form method="post" action="<?php echo $link; ?>">
				<td><h3>Organisation: </td>
				<td><h3><?php echo $tat['organization']; ?></td>	
				<td><input type="text" name="organization" placeholder="Neue Organisation"/></td>
				<td><input type="submit" name="button" value="ändern"/></td>
				</form>
			</tr>
			<tr>
				<form method="post" action="<?php echo $link; ?>">
				<td><h3>Anzahl Helfer: </td>
				<td><h3><?php echo $tat['countHelper']; ?></td>	
				<td><input type="text" name="countHelper" placeholder="Neue Anzahl Helfer"/></td>
				<td><input type="submit" name="button" value="ändern"/></td>
				</form>
			</tr>
			<tr>
				<form method="post" action="<?php echo $link; ?>">
				<td><h3>Erforderlicher Verantwortungslevel: </td>
				<td><h3><?php echo $tat['idTrust']; ?></td>	
				<td>
				<select name="idTrust" size="1">
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
				</select></td>
				<td><input type="submit" name="button" value="ändern"/></td>
				</form>
			</tr>
		</table>
		<br>
		<br>
		<br>
		<br>
		<br>
		<a href="./deeds.php"><input type="button" name="button" value="Fertig" /></a>
		</center>
		</form>
<?php require "./includes/_bottom.php"; 
//Autor dieser Funktionen : Christian Hock
//Es folgt eine lange Liste an anrufen einzelner Bestandteile von Deeds
//kommt bitte wieder zurück nach connector
function db_update_deeds_starttime($data,$idGuteTat){
		$db = db_connect();
		$sql ="UPDATE deeds
			SET 
			deeds.starttime = ?
			WHERE deeds.idGuteTat = ?";	
		$stmt = $db->prepare($sql);
		$stmt->bind_param('si',$data,$idGuteTat);
		if (!$stmt->execute()) {
			die('Fehler: ' . mysqli_error($db));
		}
		db_close($db);
}
function db_update_deeds_endtime($data,$idGuteTat){
		$db = db_connect();
		$sql ="UPDATE deeds
			SET 
			deeds.endtime = ?
			WHERE deeds.idGuteTat = ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('si',$data,$idGuteTat);
		if (!$stmt->execute()) {
			die('Fehler: ' . mysqli_error($db));
		}
		db_close($db);
}
function db_update_deeds_picture($data,$idGuteTat){
$db = db_connect();
		$sql ="UPDATE deedtexts
			SET 
			deedtexts.pictures = ?
			WHERE deedtexts.idDeedTexts = ?";	
		$stmt = $db->prepare($sql);
		$stmt->bind_param('si',$data,$idGuteTat);
		if (!$stmt->execute()) {
			die('Fehler: ' . mysqli_error($db));	
			}
			db_close($db);
	}
	function db_update_deeds_description($data,$idGuteTat){
		$db = db_connect();
		$sql ="UPDATE deedtexts
			SET 
			deedtexts.description = ?
			WHERE deedtexts.idDeedTexts = ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('si',$data,$idGuteTat);
		if (!$stmt->execute()) {
			die('Fehler: ' . mysqli_error($db));
		}
		db_close($db);
}
	function db_update_deeds_name($data,$idGuteTat){
		$db = db_connect();
		$sql ="UPDATE deeds
			SET 
			deeds.name = ?
			WHERE deeds.idGuteTat = ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('si',$data,$idGuteTat);
		if (!$stmt->execute()) {
			die('Fehler: ' . mysqli_error($db));
		}
		db_close($db);
}
	function db_update_deeds_category($data,$idGuteTat){
		$db = db_connect();
		$sql ="UPDATE deeds
			SET 
			deeds.category = ?
			WHERE deeds.idGuteTat = ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('si',$data,$idGuteTat);
		if (!$stmt->execute()) {
			die('Fehler: ' . mysqli_error($db));
		}
		db_close($db);
}
	function db_update_deeds_street($data,$idGuteTat){
		$db = db_connect();
		$sql ="UPDATE deeds
			SET 
			deeds.street = ?
			WHERE deeds.idGuteTat = ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('si',$data,$idGuteTat);
		if (!$stmt->execute()) {
			die('Fehler: ' . mysqli_error($db));
		}
		db_close($db);
}
	function db_update_deeds_housenumber($data,$idGuteTat){
		$db = db_connect();
		$sql ="UPDATE deeds
			SET 
			deeds.housenumber = ?
			WHERE deeds.idGuteTat = ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('si',$data,$idGuteTat);
		if (!$stmt->execute()) {
			die('Fehler: ' . mysqli_error($db));
		}
		db_close($db);
}
	function db_update_deeds_postalcode($data,$idGuteTat){
		$db = db_connect();
		$sql ="UPDATE deeds
			SET 
			deeds.postalcode = ?
			WHERE deeds.idGuteTat = ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('ii',$data,$idGuteTat);
		if (!$stmt->execute()) {
			die('Fehler: ' . mysqli_error($db));
		}
		db_close($db);
}
	function db_update_deeds_organization($data,$idGuteTat){
		$db = db_connect();
		$sql ="UPDATE deeds
			SET 
			deeds.organization = ?
			WHERE deeds.idGuteTat = ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('si',$data,$idGuteTat);
		if (!$stmt->execute()) {
			die('Fehler: ' . mysqli_error($db));
		}
		db_close($db);
}
	function db_update_deeds_countHelper($data,$idGuteTat){
		$db = db_connect();
		$sql ="UPDATE deeds
			SET 
			deeds.countHelper = ?
			WHERE deeds.idGuteTat = ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('ii',$data,$idGuteTat);
		if (!$stmt->execute()) {
			die('Fehler: ' . mysqli_error($db));
		}
		db_close($db);
}
	function db_update_deeds_idTrust($data,$idGuteTat){
		$db = db_connect();
		$sql ="UPDATE deeds
			SET 
			deeds.idTrust = ?
			WHERE deeds.idGuteTat = ?";
		$stmt = $db->prepare($sql);
		$stmt->bind_param('ii',$data,$idGuteTat);
		if (!$stmt->execute()) {
			die('Fehler: ' . mysqli_error($db));
		}
		db_close($db);
}
?>