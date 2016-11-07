<?php
session_start();
include './includes/db_connector.php';
require './includes/_top.php';
error_reporting(0);

// demo, es gibt noch Problem
?>
<form action="" method="get">
    Stichwort:
    <input type="text" name="st">
    <input type="submit" name="sub" value="search">
</form>
<?php
//$db = db_connect();
$db = mysqli_connect('localhost','tueGutes','Sadi23n2os','tueGutes');
if ($_GET['st']) {
    $k = explode(' ', $_GET['st']);
    $sql = "SELECT * FROM `Deeds` where name like '%$k[0]$k[1]%' or category like '%$k[0]$k[1]%'";
    $result = mysqli_query($db, $sql); ?>
    <br><br><br><br><br><br><br><br><br>
    <table style="line-height:40px;text-align:center;width:100%;font-size:20px;border:1px solid gray;clear:both">
        <caption><h1>Result</h1></caption>
        <tr style="font-size: 30px">
            <th>Gute Tat</th>
            <th>Kategorie</th>
            <th>Status</th>
        </tr>
        <tbody>
        <?php
        while ($row = mysqli_fetch_object($result)) {
            ?>
            <tr>
                <td><?php echo $row->name; ?></td>
                <td><?php echo $row->category; ?></td>
                <td><?php echo $row->status; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php mysqli_close($db);
}
?>
<!--        echo "Gute Tat:$row->name,&nbsp;Kategorie:$row->category,&nbsp;Status:$row->status";-->
<?php
require './includes/_bottom.php';
?>
