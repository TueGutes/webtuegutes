<?php
// demo, es gibt noch Problem
require './includes/_top.php';
require './db_connector.php';

$name= ($_GET['key']);
$db = db_connect();

if ($name){
    $key = explode(" ",$name);
    $sql = "SELECT * FROM `Deeds` where  like '%$k[0]%' or name like '%$k[1]' or category like '%$k[0]%' or category like '%$k[1]'";
    $result = $db->query($sql);
    db_close($db);
    $arr = array();
    while($dbentry =$result->fetch_object()){
        $arr[]= $dbentry();
    }
    return $arr;
}


 ?>

 <form action="" method="GET">
    Stichwort:
     <input type="text" name="key" >
     <input type="submit" name="sub" value="search">
 </form>
