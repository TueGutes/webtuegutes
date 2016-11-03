<?php
/*
*@author Lukas Buttke
*/

include './includes/ACCESS.php';
include './includes/db_connector.php';
require './includes/_top.php';

$idTat = 4;//$_GET["id"]; 
$tat_arr = db_getGuteTat($idTat);
?>

<?php

foreach($tat_arr AS $out) {
  echo "<br> $out";
}
count($tat_arr)

?>

<?php
require './includes/_bottom.php';
?>