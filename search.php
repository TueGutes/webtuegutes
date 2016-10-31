<?php
include "function.php";
    $db=new db();
    $db->connect();


    if ($_GET[key]) {
        $key = explode('',$_GET[key]);
        $sql = "SELECT * FROM ` ` where  like '%$k[0]%' or name like '%$k[1]'";
        echo '<br>';
        $query = $db->query($sql);
        while ($r = $db->fetch_array($query)) {
            $r[xxx] = preg_replace("/($k[0])/i", '<font color=red><b>\\1</b></font>', $r[name]);
            $r[xxx] = preg_replace("/($k[1])/i", '<font color=red><b>\\1</b></font>', $r[name]);
            echo $r[name]."<br>";
        }
    }
 ?>

 <form action="" method="GET">
    Stichwort:
     <input type="text" name="key" >
     <input type="submit" name="sub" value="search">
 </form>
