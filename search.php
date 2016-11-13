<?php
/*
*@author Shanghui Dai
*/
require './includes/DEF.php';
include './includes/db_connector.php';
require './includes/_top.php';
error_reporting(0);
?>

<!-- enter and input keyword-->
<form action="" method="post">
    <span style="font-size:20px">Stichwort:</span>
    <input type="text" name="stichwort">
    <select class="" name="selector">
        <option value="gutes">Gutes</option>
        <option value="user_name" <?php if($_POST['selector'] == 'user_name'){echo "selected";} ?>>User</option>
        <option value="ort" <?php if($_POST['selector'] == 'ort'){echo "selected";} ?>>Ort</option>
    </select>
    <input type="submit" name="sub" value="search">
</form>


<?php
$db = db_connect();

// $db = mysqli_connect('localhost', 'tueGutes', 'Sadi23n2os', 'tueGutes');
// Fuzzy Matching, die Ergebnisse in Form der Tabelle zu zeigen
if ($_POST['stichwort']) {
    $keyword = explode(' ', $_POST['stichwort']);
    if ($_POST['selector'] == 'gutes') {
        $sql = "SELECT * FROM `User` join `Deeds`
        on (`User`.idUser = `Deeds`.contactPerson)
        where `Deeds`.name like '%$keyword[0]$keyword[1]%'
        or `Deeds`.category like '%$keyword[0]$keyword[1]%'";
    } elseif ($_POST['selector'] == 'user_name') {
        $sql = "SELECT * FROM `User` join `Deeds`
        on (`User`.idUser = `Deeds`.contactPerson)
        where `User`.username like '%$keyword[0]$keyword[1]%'";
    } else {
        $sql = "SELECT * FROM `User` join `Deeds`
        on (`User`.idUser = `Deeds`.contactPerson)
        where `Deeds`.street like '%$keyword[0]$keyword[1]%'";
    }
    $result = mysqli_query($db, $sql);

//create table_header
//temporary style
//style needs to be moved into css file
    $table_header_str = '<br><br><br><br><br><br><br>';
    $table_header_str .= '<table style="line-height:40px;text-align:center;width:100%;font-size:20px;border:1px solid gray;">';
    $table_header_str .= '<caption><h1>Result</h1></caption>';
    $table_header_str .= '<tr style="font-size: 24px">';
    $table_header_str .= '<th>Gute Taten</th>';
    $table_header_str .= '<th>Kategorie</th>';
    $table_header_str .= '<th>Ersteller</th>';
    $table_header_str .= '<th>Ort</th>';
    $table_header_str .= '<th>Trust</th>';
    $table_header_str .= '<th>Status</th>';
    $table_header_str .= '</tr>';
    $table_header_str .= '<tbody>';
//table_header end

    echo $table_header_str;

//create table_body
    while ($row = mysqli_fetch_object($result)) {
        $table_body = '<tr>';
        $table_body .= '<td><a href="deeds_details?id=' . $row->idGuteTat . '">' . $row->name . '</a></td>';
        $table_body .= '<td>' . $row->category . '</td>';
        $table_body .= '<td><a href="profile?user=' . $row->username . '">' . $row->username . '</a></td>';
        $table_body .= '<td>' . $row->street . '</td>';
        $table_body .= '<td>' . $row->idTrust . '</td>';
        $table_body .= '<td>' . $row->status . '</td>';
        $table_body .= '</tr>';
    }
    $table_body .= '</tbody>';
    $table_body .= '</table>';
//table_body end

    echo $table_body.'<br>';
    db_close($db);
//    $js_selector = '<script type="text/javascript">';
//    $js_selector .= 'var selector= document.getElementsByName("selector");';
//    $js_selector .= 'selector.options["ort"].selected=true;';
//    $js_selector .= '</script>';
//    echo $js_selector;
}
?>


<!--select.options[i].selected = true;-->


<?php require "./includes/_bottom.php"; ?>
