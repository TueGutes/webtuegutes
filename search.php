<?php
include 'function.php';
    $db = new mysql();
    $db->connect('localhost', 'root', '', 'test');

    if ($_GET[KEY]) {
        $K = explode(' ', $_GET[key]);
        echo $sql = "SELECT * FROM `bbs_threads` where subject like '%$k[0]%' or subject like '%$k[1]'";
        echo '<br>';
        $q = $db->query($sql);
        while ($r = $db->fetch_array($q)) {
            $r[subject] = preg_replace("/($k[0])/i", '<font color=red><b>\\1</b></font>', $r[subject]);
            $r[subject] = preg_replace("/($k[1])/i", '<font color=red><b>\\1</b></font>', $r[subject]);
        }
    }
 ?>


 <form action="" method="get">
    Stichwort:
     <input type="text"> name="key" >
     <input tupe="text" name="sub" value="search">
 </form>
