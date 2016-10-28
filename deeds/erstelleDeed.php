<html> 
<head> <title> Deeds erstellen </title>
<head>

<body>

<h2> Gute Tat erstellen
<form action"" method="post">
<input name="id" type="number" placeholder="ID der Guten Tat"> <br> 
<input name="name" type="text" placeholder="Name des Auftraggebers"> <br>
<input name="contactPerson" type="number" placeholder="ID der Kontakperson"> <br>
<input name="category" type="text" placeholder="Kategorie der ´Guten Tat"> <br>
<input name="street" type="text" placeholder="Straße für die Gute Tat"> <br>
<input name="housenumber" type="text" placeholder="Hausnummer für die Gute Tat"> <br>
<input name="postalcode" type="text" placeholder="PLZ der Guten Tat"> <br>
<input name="time" type="datetime-local" placeholder="aktueller Zeitpunkt"> <br>
<input name="organization" type="text" placeholder="Organisation für die Gute Tat"> <br>
<input name="countHelper" type="number" placeholder="Anzahl der Helfer"> <br>
<input name="idTrust" type="number" placeholder="Benötiges Vertrauen"> <br>
<input name="status" type="text" placeholder="Enum a, b oder c "> <br>
<input type="submit" value="Gute Tat erstellen">
</form>


<?php

/*
*@author Lukas Buttke
*/

include 'db_connector.php';

$id = $_POST['id'];
$name = $_POST['name'];
$con = $_POST['contactPerson'];
$cat = $_POST['category'];
$s = $_POST['street'];
$hn = $_POST['housenumber'];
$p = $_POST['housenumber'];
$time = $_POST['time'];
$org = $_POST['organization'];
$help = $_POST['countHelper'];
$tr = $_POST['idTrust'];
$stat = $_POST['status'];


$db = db_connect();
$sql = "Insert into Deeds values($id,$name,$con,$cat,$s,$hn,$p,$time,$org,$help,$tr,$stat)";
$stmt = $db->prepare($sql);
$stmt->execute();

?>

</body>
</html>
