<?php
include './includes/db_connector.php';
require './includes/_top.php';
error_reporting(0);

// demo, es gibt noch Problem
// Man muss die URL in href weiter einfuegen.
?>
<form action="" method="get">
    <span style="font-size:20px">Stichwort:</span>
    <input type="text" name="st">
    <select class="" name="selector">
        <option value="gutes">Gutes</option>
        <option value="user_name">User</option>
        <option value="ort">Ort</option>
    </select>
    <input type="submit" name="sub" value="search">
</form>
<?php
//$db = db_connect();
$db = mysqli_connect('localhost', 'tueGutes', 'Sadi23n2os', 'tueGutes');
if ($_GET['st']) {
    if ($_GET['selector'] == 'gutes') {
        $k = explode(' ', $_GET['st']);
        $sql = "SELECT * FROM `Deeds` where name like '%$k[0]$k[1]%' or category like '%$k[0]$k[1]%'";
        $result = mysqli_query($db, $sql); ?>
    <br><br><br><br><br><br><br>
    <table style="line-height:40px;text-align:center;width:100%;font-size:20px;border:1px solid gray;clear:both">
        <caption><h1>Result</h1></caption>
        <tr style="font-size: 24px">
            <th>Gute Taten</th>
            <th>Kategorie</th>
            <th>Ersteller</th>
            <th>Ort</th>
            <th>Trust</th>
            <th>Status</th>
        </tr>
        <tbody>
        <?php
        while ($row = mysqli_fetch_object($result)) {
            ?>
            <tr>
                <td><a href=""><?php echo $row->name; ?></a></td>
                <td><?php echo $row->category; ?></td>
                <td><a href=""><?php echo $row->contactPerson;?></a></td>
                <td><a href=""><?php echo $row->street;?></a></td>
                <td><?php echo $row->idTrust; ?>
                <td><?php echo $row->status; ?></td>
                </td>
            </tr>
        <?php
        } ?>
        </tbody>
    </table>
    <?php mysqli_close($db);
    }
}
?>
<!--        echo "Gute Tat:$row->name,&nbsp;Kategorie:$row->category,&nbsp;Status:$row->status";-->
<?php
require './includes/_bottom.php';
?>
