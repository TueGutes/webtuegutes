<?php
/*
*@author Christian Hock
* das Bild und den Zeitrahmen zu ändern wurde ausgelassen, da man diese momentan nicht erstellen kann.
* Es wird die Funktion db_fix_plz($plz) genutzt...
*/

require './includes/DEF.php';

include './includes/ACCESS.php';

require './includes/db_connector.php';

require './includes/_top.php';

//Fehlerüberprüfung auf den Getparameter und prüfung ob die Seite ein zweites mal aufgerufen wurde
if (!isset($_GET['id'])){
echo '<h3>Es ist ein Aufruffehler aufgetreten.<br>Vermutlich haben Sie den Link für diese Seite manuell eingegeben, bzw. herauskopiert und dabei einen Fehler gemacht.<br>Oder die Tat gibt es nicht mehr.<br>Sollte weder noch zutreffen kontaktieren Sie uns bitte.</h3><br>';	
	}else{
$idGuteTat = $_GET['id'];
	}
//Tat-Objekt holen	für Nutzerüberprüfung
	$tat = db_getGuteTat($idGuteTat);

//Prüft welche Tat betroffen ist und fängt Fehler ab 
//Beschreibung
 echo "<h2>Deine Tat bearbeiten </h2>";
 if (isset($_POST['description'])) {
	$data=$_POST['description'];
	if ($data === ''){
		echo '<h3>Bitte eine neue Beschreibung eingeben.</h3><br>';
	}else{
	db_update_deeds($data,$idGuteTat,'0');}}
//Name
 else if (isset($_POST['name'])) {
	$data=$_POST['name'];
	if(db_doesGuteTatNameExists($data)){
		echo '<h3>Eine andere Tat ist bereits unter diesem Namen veröffentlicht.</h3>';
	}else if ($data === ''){
		echo '<h3>Bitte einen neuen Namen eingeben.</h3><br>';
	}else{
	db_update_deeds($data,$idGuteTat,'2');}}
//Kategorie
else if(isset($_POST['category'])) {
	$data=$_POST['category'];
	db_update_deeds($data,$idGuteTat,'4');}
//Straße
	else if(isset($_POST['street'])) {
	$data=$_POST['street'];
	if ($data === ''){
	echo '<h3>Bitte eine neue Straße eingeben.</h3><br>';	
	}else{
	db_update_deeds($data,$idGuteTat,'5');}
	}
//Hausnummer
else if(isset($_POST['housenumber'])) {
	$data=$_POST['housenumber'];
	if ($data === ''){
	echo '<h3>Bitte eine neue Hausnummer eingeben.</h3><br>';	
	}else{
	db_update_deeds($data,$idGuteTat,'6');}
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
	db_update_deeds($data,$idGuteTat,'7');}
	}
//Organisation
else if(isset($_POST['organization'])) {
	$data=$_POST['organization'];
	if ($data === ''){
	echo '<h3>Bitte eine neue Organisation eingeben.</h3><br>';	
	}else{
	db_update_deeds($data,$idGuteTat,'9');}	
	}
//Anzahl Helfer
else if(isset($_POST['countHelper'])) {
	$data=$_POST['countHelper'];
	if ($data === ''){
	echo '<h3>Bitte eine neue Anzahl an Helfern eingeben.</h3><br>';	
	}else if (!is_numeric($data)){
		echo '<h3>Die Anzahl der gewünschten Helfer bitte als Zahl eingeben.</h3><br>';	
	}else{
	db_update_deeds($data,$idGuteTat,'10');}
	}
//Verantwortungslevel
else if(isset($_POST['idTrust'])) {
	$data=$_POST['idTrust'];
	db_update_deeds($data,$idGuteTat,'11');}
//Tat-Objekt holen -> jetzt damit es akutell ist
	$tat = db_getGuteTat($idGuteTat);	
//link zusammenbauen


//Autor dieser Methode : Christian Hock
//Einzelne Tat bearbeiten -> Diese Methode finded Anwendung bei der tat_bearbeiten.php-Seite
//$data -> neuer Inhalt, $idGuteTat -> logo, $Spalte-> Spalte innerhalb der Deedstabelle, $Variablentyp-> gibt an welchen Typ die Variable hat
//&Spalte 0 entsrpricht der Beschreibung der Tat
function db_update_deeds($data,$idGuteTat,$Spalte)
	{
		$Variablentyp='s';
		$db = db_connect();
	if($Spalte==0){
		$sql ="UPDATE deedtexts
			SET 
			deedtexts.description = ?
			WHERE deedtexts.idDeedTexts = ?";
		
		
	}else if($Spalte==2){
		$sql ="UPDATE deeds
			SET 
			deeds.name = ?
			WHERE deeds.idGuteTat = ?";
	}else if($Spalte==4){
		$sql ="UPDATE deeds
			SET 
			deeds.category = ?
			WHERE deeds.idGuteTat = ?";
	}
	else if($Spalte==5){
		$sql ="UPDATE deeds
			SET 
			deeds.street = ?
			WHERE deeds.idGuteTat = ?";
	}
	else if($Spalte==6){
		$sql ="UPDATE deeds
			SET 
			deeds.housenumber = ?
			WHERE deeds.idGuteTat = ?";
	}
	else if($Spalte==7){
		$sql ="UPDATE deeds
			SET 
			deeds.postalcode = ?
			WHERE deeds.idGuteTat = ?";
			$Variablentyp='i';
	}
	else if($Spalte==9){
		$sql ="UPDATE deeds
			SET 
			deeds.organization = ?
			WHERE deeds.idGuteTat = ?";
	}
	else if($Spalte==10){
		$sql ="UPDATE deeds
			SET 
			deeds.countHelper = ?
			WHERE deeds.idGuteTat = ?";
			$Variablentyp='i';
	}
	else if($Spalte==11){
		$sql ="UPDATE deeds
			SET 
			deeds.idTrust = ?
			WHERE deeds.idGuteTat = ?";
			$Variablentyp='i';
	}
		$stmt = $db->prepare($sql);
		if($Variablentyp=='s')$stmt->bind_param('si',$data,$idGuteTat);
		if($Variablentyp=='i')$stmt->bind_param('ii',$data,$idGuteTat);
		if (!$stmt->execute()) {
			die('Fehler: ' . mysqli_error($db));
		}
		db_close($db);
	}


$link = './deeds_bearbeiten?id=' . $idGuteTat;
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
				<td><h3>Zeitrahmen:WarteBisZeitformatklar </td>
				<td><h3><?php echo $tat['time']; ?></td>	
				<td><input type="text" name="name" placeholder="neuer Zeitrahmen"/></td>
				<td><input type="submit" name="button" value="ändern"/></td>
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
/*Bitte in den Db-Connector einfügen

//Autor dieser Methode : Christian Hock
//Einzelne Tat bearbeiten -> Diese Methode finded Anwendung bei der tat_bearbeiten.php-Seite
//$data -> neuer Inhalt, $idGuteTat -> logo, $Spalte-> Spalte innerhalb der Deedstabelle, $Variablentyp-> gibt an welchen Typ die Variable hat
//&Spalte 0 entsrpricht der Beschreibung der Tat
function db_update_deeds($data,$idGuteTat,$Spalte)
	{
		$Variablentyp='s';
		$db = db_connect();
	if($Spalte==0){
		$sql ="UPDATE deedtexts
			SET 
			deedtexts.description = ?
			WHERE deedtexts.idDeedTexts = ?";
		
		
	}else if($Spalte==2){
		$sql ="UPDATE deeds
			SET 
			deeds.name = ?
			WHERE deeds.idGuteTat = ?";
	}else if($Spalte==4){
		$sql ="UPDATE deeds
			SET 
			deeds.category = ?
			WHERE deeds.idGuteTat = ?";
	}
	else if($Spalte==5){
		$sql ="UPDATE deeds
			SET 
			deeds.street = ?
			WHERE deeds.idGuteTat = ?";
	}
	else if($Spalte==6){
		$sql ="UPDATE deeds
			SET 
			deeds.housenumber = ?
			WHERE deeds.idGuteTat = ?";
	}
	else if($Spalte==7){
		$sql ="UPDATE deeds
			SET 
			deeds.postalcode = ?
			WHERE deeds.idGuteTat = ?";
			$Variablentyp='i';
	}
	else if($Spalte==9){
		$sql ="UPDATE deeds
			SET 
			deeds.organization = ?
			WHERE deeds.idGuteTat = ?";
	}
	else if($Spalte==10){
		$sql ="UPDATE deeds
			SET 
			deeds.countHelper = ?
			WHERE deeds.idGuteTat = ?";
			$Variablentyp='i';
	}
	else if($Spalte==11){
		$sql ="UPDATE deeds
			SET 
			deeds.idTrust = ?
			WHERE deeds.idGuteTat = ?";
			$Variablentyp='i';
	}
		$stmt = $db->prepare($sql);
		if($Variablentyp=='s')$stmt->bind_param('si',$data,$idGuteTat);
		if($Variablentyp=='i')$stmt->bind_param('ii',$data,$idGuteTat);
		if (!$stmt->execute()) {
			die('Fehler: ' . mysqli_error($db));
		}
		db_close($db);
	}

?>
*/
?>