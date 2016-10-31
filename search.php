<?php
// demo, es gibt noch Problem
require './includes/_top.php';
require './db_connector.php';

$name = ($_GET['key']);
$db = db_connect();

if ($name) {
    $key = explode(' ', $name);
    $sql = "SELECT * FROM `Deeds` where  like '%$key[0]$key[1]%' or name like '%$key[1]$key[2]%' or category like '%$key[0]$key[1]%' or category like '%$key[1]$key[2]%'";
    $result = $db->query($sql);
    db_close($db);
    $arr = array();
    while ($dbentry = $result->fetch_object()) {
        $arr[] = $dbentry();
    }

    return $arr;
}

 ?>

 <form action="" method="GET">
    Stichwort:
     <input type="text" name="key" >
     <input type="submit" name="sub" value="search">
 </form>
