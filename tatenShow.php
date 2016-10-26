<?php

require './includes/_top.php';

//Inkludieren von script-Dateien
include 'db_connector.php';

$db = db_connect();
$sql = "SELECT * FROM Tatem ";
$stmt = $db->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

//Auslesen des Ergebnisses
$dbentry = $result->fetch_assoc();
switch

require './includes/_bottom.php';
?>