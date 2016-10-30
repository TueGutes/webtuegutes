<?php
/*
*@author Klaus Sobotta
*/

// die session geht nicht brauche da hilfe
//if($_SESSION){
	
//Inkludieren von script-Dateien
require './includes/_top.php';

include './includes/db_connector.php';
include './includes/emailSender.php';


//Variablen init
$name= ($_GET['name']);
$contactPerson= $_GET['contactPerson'];
$category= $_GET['category'];
$street= $_GET['street'];
$housenumber= $_GET['housenumber'];
$postalcode= $_GET['postalcode'];
$time_t= $_GET['time'];
$organization= $_GET['organization'];
$countHelper= $_GET['countHelper'];
$idTrust= $_GET['idTrust'];
$status= $_GET['status'];

?>

<?php
function verbindenMitDB($name,$contactPerson,$category,$street,$housenumber,$postalcode,$time_t,$organization,$countHelper,$idTrust,$status){
$servername="localhost";
$serverusername="root";
$serveruserpw="";
$dbname="tuegutesdb";


$con= new mysqli($servername,$serverusername,$serveruserpw,$dbname);
	if($con->connect_error){
		die("Es ist etwas schief gelaufen");
	}
	
	// import eine gute tat in die daten bank
	$sql="INSERT INTO deeds (idGuteTat,name,contactPerson,category,street,housenumber,postalcode,time,organization,countHelper,idTrust,status) VALUES ((SELECT MAX(idGuteTat) FROM deeds),?,?,?,?,?,?,?,?,?,?,?)";
	$stmt = $con->prepare($sql);
	mysqli_stmt_bind_param($stmt, "isisssissiis",$idGutetat,$name,$contactPerson,$category,$street,$housenumber,$postalcode,$time_t,$organization,$countHelper,$idTrust,$status);
	$stmt->execute();
	$affected_rows = mysqli_stmt_affected_rows($stmt);
	
	echo "Ein moment bitte";
	if($con->query($sql)=== TRUE) {
	?>
		
		<h2><?php echo "Die Tat wurde Erstellt"; ?></h2>
		<html>
		<body>
		<?//die start seite muss noch definirt werden?>
		<form action="./login.php">
		<br><input type="submit"  value="zur Startseite"/><br>
		</form>
		</html>
		</body>
		<?php
	}else{
		?>
		
		<h2><?php echo "Es ist ein fehler aufgetreten"; ?></h2>
		<html>
		<body>
		<form action="./guteTatErstellenHTML.php">
		<br><input type="submit" value="Nochmal Versuchen"/><br>
		</form>
		<?//die start seite muss noch definirt werden?>
		<form action="./login.php">
		<br><input type="submit" value="zur Startseite"/><br>
		</form>
		</html>
		</body>
		<?php
		echo $con->error;
	}
	$con->close();
}
?>


<?php

verbindenMitDB($name , $contactPerson,$category,$street,$housenumber,$postalcode,$time_t,$organization,$countHelper,$idTrust,$status);

?>



<?
require './includes/_bottom.php';

/*}else{
	// rückfürung zur einlock seite
}*/

?>

