<?php
/*
*@author Lukas Buttke
*/

include './includes/ACCESS.php';
include './includes/db_connector.php';
require './includes/_top.php';

$idTat = $_GET["id"];
$Tat_obj = db_getGuteTat($idTat);
?>


<?php
require './includes/_bottom.php';
?>