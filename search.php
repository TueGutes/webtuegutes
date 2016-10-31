<?php
include "objektorientiertes_beispiel/db.php";
    $db=new DB();
    db->connect("localhost","","root","");


    if ($_GET[key] {
        $key = explode(' ', $_GET[key]);
        $sql = "SELECT * FROM ` ` where  like '%$k[0]%' or name like '%$k[1]'";
        echo '<br>';
        $q = $db->query($sql);
        while ($r = $db->fetch_array($q)) {
            $r[xxx] = preg_replace("/($k[0])/i", '<font color=red><b>\\1</b></font>', $r[name]);
            $r[xxx] = preg_replace("/($k[1])/i", '<font color=red><b>\\1</b></font>', $r[name]);
        }
    }
 ?>


 <form action="" method="GET">
    Stichwort:
     <input type="text" name="key" >
     <input type="submit" name="sub" value="search">
 </form>
