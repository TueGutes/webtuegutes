<?php
/*
*@author Shanghui Dai
*/
require_once './includes/DEF.php';
include_once './includes/db_connector.php';
require_once './includes/_top.php';
error_reporting(0);
?>

<!-- enter and input keyword-->
<form action="" method="get">
    <span style="font-size:20px">Stichwort:</span>
    <input type="text" name="stichwort">
    <select class="" name="selector">
        <option value="gutes">Gutes</option>
        <option value="user_name"
        <?php if ($_POST['selector'] == 'user_name') {echo 'selected';}?>>User
        </option>
        <option value="ort"
        <?php if ($_POST['selector'] == 'ort') {echo 'selected';} ?>>Ort
        </option>
    </select>
    <input type="submit" name="sub" value="search">
</form>


<?php
$db = DBFunctions::db_connect();

// $db = mysqli_connect('localhost', 'tueGutes', 'Sadi23n2os', 'tueGutes');
// Fuzzy Matching, die Ergebnisse in Form der Tabelle zu zeigen

if ($_GET['stichwort']) {
    $keyword = explode(' ', $_GET['stichwort']);
    if ($_GET['selector'] == 'gutes') {
        $sql = "SELECT DISTINCT * FROM `User` join `Deeds`
        on (`User`.`idUser` = `Deeds`.`contactPerson`) join `Postalcode`
        on(`Deeds`.`idPostal` = `Postalcode`.`idPostal`)

        where `Deeds`.name like '%$keyword[0]$keyword[1]%'
        or `Deeds`.category like '%$keyword[0]$keyword[1]%'
        ORDER BY `Deeds`.category";
    } elseif ($_GET['selector'] == 'user_name') {
        $sql = "SELECT DISTINCT * FROM `User` join `Deeds`
        on (`User`.idUser = `Deeds`.contactPerson) join `Postalcode`
        on(`Deeds`.`idPostal` = `Postalcode`.`idPostal`)
        where `User`.username like '%$keyword[0]$keyword[1]%'
        ORDER BY `Deeds`.category";
    } else {
        $sql = "SELECT DISTINCT * FROM `User` join `Deeds`
        on (`User`.idUser = `Deeds`.contactPerson) join `Postalcode`
        on(`Deeds`.`idPostal` = `Postalcode`.`idPostal`)
        where `Deeds`.street like '%$keyword[0]$keyword[1]%'
        ORDER BY `Deeds`.category";
    }
    $result = mysqli_query($db, $sql);
    $num = mysqli_num_rows($result);

    // you can change pagesize here, 5 is now a very small number but it's easy to see changes
    $pagesize = 2;

    //calculate how many pages we need
    $maxpage = ceil($num / $pagesize);
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    if ($page < 1) {
        $page = 1;
    }
    if ($page > $maxpage) {
        $page = $maxpage;
    }
    //get one page
    $limit = ' limit '.($page - 1) * $pagesize.','.$pagesize * $page;
    $sql2 = $sql." {$limit}";
    $res = mysqli_query($db, $sql2);

//create table_header
//temporary style
//style needs to be moved into css file

    $table_header_str = '<br><br><br><br><br><br><br>';
    $table_header_str .= '<table style="line-height:40px;text-align:center;width:100%;font-size:20px;border:1px solid gray;">';
    $table_header_str .= '<caption><h1>Result</h1></caption>';
    $table_header_str .= '<tr style="font-size: 24px">';
    $table_header_str .= '<th>Taten</th>';
    $table_header_str .= '<th>Kategorie</th>';
    $table_header_str .= '<th>User</th>';
    $table_header_str .= '<th>Stadtteil</th>';
    $table_header_str .= '<th>Trust</th>';
    $table_header_str .= '<th>Startzeit</th>';
    $table_header_str .= '<th>Endzeit</th>';
    $table_header_str .= '<th>Status</th>';
    $table_header_str .= '</tr>';
    $table_header_str .= '<tbody>';
//table_header end

    echo $table_header_str;

//create table_body
    $table_body;
    while ($row = mysqli_fetch_array($res)) {
        $table_body .= '<tr>';
        $table_body .= '<td><a href="deeds_details?id='.$row['idGuteTat'].'">'.$row['name'].'</a></td>';
        $table_body .= '<td>'.$row['category'].'</td>';
        $table_body .= '<td><a href="profile?user='.$row['username'].'">'.$row['username'].'</a></td>';
        $table_body .= '<td>'.$row['place'].'</td>';
        $table_body .= '<td>'.$row['idTrust'].'</td>';
        $table_body .= '<td>'.$row['starttime'].'</td>';
        $table_body .= '<td>'.$row['endtime'].'</td>';
        $table_body .= '<td>'.$row['status'].'</td>';
        $table_body .= '</tr>';
    }
    $table_body .= '</tbody>';
    $table_body .= '</table>';
//  table_body end
    echo $table_body.'<br>';
    mysqli_free_result($result);
    mysqli_free_result($res);
//  pages
//    echo "<a href='search.php?page=1&stichwort=".$_GET['stichwort']."&selector=".$_GET['selector']."'style='margin-left:30px;font-size:20px'>first</a> ";
//    echo "<a href='search.php?page=".($page-1)."&stichwort=".$_GET['stichwort']."&selector=".$_GET['selector']."' style='margin-left:30px;font-size:20px'>previous</a>";
//    echo "<a href='search.php?page=".($page+1)."&stichwort=".$_GET['stichwort']."&selector=".$_GET['selector']."' style='margin-left:30px;font-size:20px'>next</a>";
//    echo "<a href='search.php?page={$maxpage}&stichwort=".$_GET['stichwort']."&selector=".$_GET['selector']."' style='margin-left:30px;font-size:20px'>last</a>";
//set page URLs

    echo setPageUrl(1, 'first');
    echo setPageUrl($page - 1, 'previous');
    echo setPageUrl($page + 1, 'next');
    echo setPageUrl($maxpage, 'last');
    DBFunctions::db_close($db);
//    $js_selector = '<script type="text/javascript">';
//    $js_selector .= 'var selector= document.getElementsByName("selector");';
//    $js_selector .= 'selector.options["ort"].selected=true;';
//    $js_selector .= '</script>';
//    echo $js_selector;
}

//this function will be moved to file includes/db_connector.php after all job been done
function setPageUrl($page, $sort)
{
    $str = "<a href='search.php?page=".$page.'&stichwort='.$_GET['stichwort'].'&selector='.$_GET['selector'].
        "' style='margin-left:30px;font-size:20px'>".$sort.'</a>';
    return $str;
}

?>

<?php require './includes/_bottom.php'; ?>
