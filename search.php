<?php
session_start();
include './includes/db_connector.php';
require './includes/_top.php';
error_reporting(0);

// demo, es gibt noch Problem
?>
<form action="" method="get">
   Stichwort:
    <input type="text" name="st" >
    <input type="submit" name="sub" value="search">
</form>
<?php
$db = db_connect();
if ($_GET['st']) {
    $k = explode(' ',$_GET['st']);
    $sql = "SELECT * FROM `Deeds` where name like '%$k[0]$k[1]%' or category like '%$k[0]$k[1]%'";
    $result = mysqli_query($db, $sql);
    echo "<br>";echo "<br>";echo "<br>";echo "<br>";echo "<br>";echo "<br>";echo "<br>";echo "<br>";
    while ($row=mysqli_fetch_object($result)) {
        echo "Gute Tat:$row->name,&nbsp;Kategorie:$row->category,&nbsp;Status:$row->status";
    }
    db_close($db);
}
?>

 <?php
 require './includes/_bottom.php';
 ?>
