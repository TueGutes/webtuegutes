<?php
/*
*@author Klaus Sobotta
*@author Christian Hock
*/

//if($_SESSION){
	
require './includes/_top.php';
?>

<h2><?php echo "Eine Tat Erstellen"; ?></h2>


<html>
<title>Tat erstellen</title>
<body>
<form methode="POST" action="./guteTatErstellenDB.php" enctype="multipart/form-data">
	<center>
	<h3>Bitte alle Felder ausfüllen =)</h3>
	<br>
	<br>
	<table>
		<tr>
			<td><h3>*Name der Tat: </td>
			<td><input type="text" name="name" placeholder="Name"/></td>
		</tr>
		<tr>
			<td><h3>*Beschreibung: </td>
			<td></td>
		</tr>
		<tr>
			<td colspan="2"><textarea id="text" name="description" cols="80" rows="6" placeholder="Beschreiben Sie die auszuführende Tat. Werben Sie für Ihr Angebot. Nutzen sie ggf. eine Rechtschreibüberprüfung."></textarea></td>
		</tr>
		<tr>
			<td><h3>*Bild:</td>
			<td><input name="pictures" type="file" size="50" accept="text/*"> </td>
		</tr>
		<tr>
			<td><h3>*Kontaktperson:!soll eig. automatisch passieren </td>
			<td><input type="text" name="contactPerson" placeholder="Kontaktperson"/></td>
		</tr>
		<tr>
			<td><h3>*Kategorie:</td>
			<td><select name="kategorie" size="1">
			<option value="Altenheim">Altenheim</option>
			<option value="Busbahnhof">Busbahnhof</option>
			<option value="Mülleinsammeln">Mülleinsammeln</option>
			</select></label></td></td>
		</tr>
		<tr>
			<td><h3>*Straße:</td>
			<td><input type="text" name="street" placeholder="Straßenname"/></td>
		</tr>
		<tr>	
			<td><h3>*Hausnummer:</td>
			<td><input type="text" name="housenumber" placeholder="Hausnummer"/></td>
		</tr>
		<tr>
			<td><h3>*Postleitzahl</td>
			<td><input type="text" name="postalcode" placeholder="Postleitzahl"/></td>
		</tr>
		<tr>
			<td><h3>*Zeitrahmen</td>
			<td><input type="text" name="time" placeholder="Zeitrahmen"/></td>
		</tr>
		<tr>
			<td><h3>Organisation:</td>
			<td><input type="text" name="organization" placeholder="Organisation"/></td>
		</tr>
		<tr>
			<td><h3>Anzahl Helfer:</td>
			<td><input type="text" name="countHelper" placeholder="Zähler"/></td>
		</tr>
			<tr>
			<td><h3>*erforderlicher Verantwortungslevel:</td>
			<td><select name="tat_verantwortungslevel" size="1">
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			</select></label></td>
		</tr>
		
</table>
		<td><a href="./index.php"><input type="button" name="button" value="Zurück" /></a></td>	
<td><a href="./Taterstellt.html"><input type="submit" name="button" value="Tat erstellen"/></a></td>
</center>
</form>
</html>
</body>
<?php
require './includes/_bottom.php';
?>
