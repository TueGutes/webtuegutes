<?php
/*
*@author Shanghui Dai
*/
require_once './includes/DEF.php';
include_once './includes/db_connector.php';
require_once './includes/_top.php';
error_reporting(0);
date_default_timezone_set("Europe/Berlin");
?>

<!----------------  enter and input keyword  ----------------->
<!---------------- if you choose 'Zeit', the style will change-------------->



<form action="" method="get" id="form">
    <span style="font-size:20px" name="keyword_text">
        <?php if ($_GET['selector'] == 'zeit')
            {echo('Zeitpunkt:');}
            else{echo('Stichwort:');}?>
    </span>
    <?php if ($_GET['selector'] == 'zeit') {
        echo('<input type="datetime-local" name="stichwort"');
        if ($_GET['stichwort'] != '') {
            echo ' value="' . $_GET['stichwort'] . '"';
        }
        echo '>';
    }

    else{echo('<input type="text" name="stichwort">');}?>
    <select class="" name="selector" onchange="setTimeLabel(this)">
        <option value="gutes">Gutes</option>
        <option value="user_name"
        <?php if ($_GET['selector'] == 'user_name') {echo 'selected';}?>>User
        </option>
        <option value="ort"
        <?php if ($_GET['selector'] == 'ort') {echo 'selected';} ?>>Ort
        </option>
        <option value="zeit"
            <?php if ($_GET['selector'] == 'zeit') {echo 'selected';} ?>>Zeit
        </option>
    </select>
    <input type="submit" name="sub" value="search">
</form>


<!---------------  change Style when 'Zeit' ist selected  ------------------>


<script type="text/javascript">
    function setTimeLabel(event) {
        var form = document.getElementById("form");
        var keyword_text = document.getElementsByName("keyword_text");
        var keyword = document.getElementsByName("stichwort");
        var origin = document.createElement("input");
        if (event.value == "zeit") {
            var time = document.createElement("input");
            var format = getFormat();
            time.type = "datetime-local";
            time.name = "stichwort";
            time.value = format;
            keyword_text[0].innerHTML = "Zeitpunkt:";
            keyword_text[0].parentNode.replaceChild(time,keyword[0]);

        }else{
            var origin = document.createElement("input");
            origin.type = "text";
            origin.name = "stichwort";
            keyword_text[0].innerHTML = "Stichwort:";
            keyword_text[0].parentNode.replaceChild(origin,keyword[0]);
        }
    }


//--------------  set default timedate(current time)  -------------------


    function getFormat(){
        var format = "";
        var nTime = new Date();
        format += nTime.getFullYear()+"-";
        format += (nTime.getMonth()+1)<10?"0"+(nTime.getMonth()+1):(nTime.getMonth()+1);
        format += "-";
        format += nTime.getDate()<10?"0"+(nTime.getDate()):(nTime.getDate());
        format += "T";
        format += nTime.getHours()<10?"0"+(nTime.getHours()):(nTime.getHours());
        format += ":";
        format += nTime.getMinutes()<10?"0"+(nTime.getMinutes()):(nTime.getMinutes());
        format += ":00";
        return format;
    }

</script>




<?php
$db = DBFunctions::db_connect();

// $db = mysqli_connect('localhost', 'tueGutes', 'Sadi23n2os', 'tueGutes');
// -------------------  Fuzzy Matching, die Ergebnisse in Form der Tabelle zu zeigen  --------------------
//--------------------  search through keywords,username,place or time  -----------------
//--------------------  the head of sql-queries like "SELECT DISTINCT * "will be re-changed in the future--------------

if ($_GET['stichwort']) {
    if ($_GET['selector'] == 'zeit') {
        $arr = explode('T', $_GET['stichwort']);
        $keyword = $arr[0] . " " . $arr[1] . ":00";
    } else {
        $keyword = explode(' ', $_GET['stichwort']);
    }
    if ($_GET['selector'] == 'gutes') {
        $sql = "SELECT DISTINCT * FROM `User` JOIN `Deeds`
        ON (`User`.`idUser` = `Deeds`.`contactPerson`) JOIN `Postalcode`
        ON (`Deeds`.`idPostal` = `Postalcode`.`idPostal`) JOIN `DeedTexts`
        ON (`Deeds`.`idGuteTat`=`DeedTexts`.`idDeedTexts`)
        WHERE `Deeds`.`name` like '%$keyword[0]$keyword[1]%'
        OR `Deeds`.`category` like '%$keyword[0]$keyword[1]%'
        ORDER BY `Deeds`.`category`, `Deeds`.`starttime`";
    } elseif ($_GET['selector'] == 'user_name') {
        $sql = "SELECT DISTINCT * FROM `User` JOIN `Deeds`
        ON (`User`.`idUser` = `Deeds`.contactPerson) JOIN `Postalcode`
        ON (`Deeds`.`idPostal` = `Postalcode`.`idPostal`) JOIN `DeedTexts`
        ON (`Deeds`.`idGuteTat`=`DeedTexts`.`idDeedTexts`)
        WHERE `User`.`username` like '%$keyword[0]$keyword[1]%'
        ORDER BY `Deeds`.`category`, `Deeds`.`starttime`";
    } else if ($_GET['selector'] == 'ort') {
        $sql = "SELECT DISTINCT * FROM `User` JOIN `Deeds`
        ON (`User`.`idUser` = `Deeds`.contactPerson) JOIN `Postalcode`
        ON (`Deeds`.`idPostal` = `Postalcode`.`idPostal`) JOIN `DeedTexts`
        ON (`Deeds`.`idGuteTat`=`DeedTexts`.`idDeedTexts`)
        WHERE `Deeds`.`street` like '%$keyword[0]$keyword[1]%'
        OR `Postalcode`.`place` like '%$keyword[0]$keyword[1]%'
        ORDER BY `Deeds`.`category`, `Deeds`.`starttime`";
    } else {
        $sql = " SELECT DISTINCT * FROM `User` JOIN `Deeds`
        ON (`User`.`idUser` = `Deeds`.contactPerson) JOIN `Postalcode`
        ON (`Deeds`.`idPostal` = `Postalcode`.`idPostal`) JOIN `DeedTexts`
        ON (`Deeds`.`idGuteTat`=`DeedTexts`.`idDeedTexts`)
        WHERE `Deeds`.`starttime` < '$keyword'
        AND `Deeds`.`endtime` > '$keyword'
        ORDER BY  `Deeds`.`starttime`,`Deeds`.`category`";
    }
    $result = mysqli_query($db, $sql);
    $num = mysqli_num_rows($result);




    // -------------  you can change pagesize here, 5 is now a very small number but it's easy to see changes  -----------

    $pagesize = 5;

    //--------------  calculate how many pages we need  ----------------


    $maxpage = ceil($num / $pagesize);
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    if ($page < 1) {
        $page = 1;
    }
    if ($page > $maxpage) {
        $page = $maxpage;
    }


    //-------------- get one page ----------------


    $limit = ' limit ' .(($page-1)*$pagesize).','.$pagesize;
    $sql2 = $sql . " {$limit}";
    $res = mysqli_query($db, $sql2);
    if ($res == null) {
        echo "<br><br><br><br>No Result";
        mysqli_free_result($result);
        mysqli_free_result($res);
    } else {



//---------------  show results in table form  ----------------

//    $table_header_str = '<br><br><br><br><br><br><br>';
//    $table_header_str .= '<table style="line-height:40px;text-align:center;width:100%;font-size:20px;border:1px solid gray;">';
//    $table_header_str .= '<caption><h1>Result</h1></caption>';
//    $table_header_str .= '<tr style="font-size: 24px">';
//    $table_header_str .= '<th>Taten</th>';
//    $table_header_str .= '<th>Kategorie</th>';
//    $table_header_str .= '<th>User</th>';
//    $table_header_str .= '<th>Stadtteil</th>';
//    $table_header_str .= '<th>Trust</th>';
//    $table_header_str .= '<th>Startzeit</th>';
//    $table_header_str .= '<th>Endzeit</th>';
//    $table_header_str .= '<th>Status</th>';
//    $table_header_str .= '</tr>';
//    $table_header_str .= '<tbody>';
////table_header end
//
//    echo $table_header_str;
//
////create table_body
//    $table_body;
//    while ($row = mysqli_fetch_array($res)) {
//        $table_body .= '<tr>';
//        $table_body .= '<td><a href="deeds_details?id='.$row['idGuteTat'].'">'.$row['name'].'</a></td>';
//        $table_body .= '<td>'.$row['category'].'</td>';
//        $table_body .= '<td><a href="profile?user='.$row['username'].'">'.$row['username'].'</a></td>';
//        $table_body .= '<td>'.$row['place'].'</td>';
//        $table_body .= '<td>'.$row['idTrust'].'</td>';
//        $table_body .= '<td>'.$row['starttime'].'</td>';
//        $table_body .= '<td>'.$row['endtime'].'</td>';
//        $table_body .= '<td>'.$row['status'].'</td>';
//        $table_body .= '</tr>';
//    }
//    $table_body .= '</tbody>';
//    $table_body .= '</table>';
//  table_body end
//    echo $table_body.'<br>';


// ------------------ show results in Deeds_list form  -----------------

        $result_str = "<br><br><br><br>";
        while ($row = mysqli_fetch_array($res)) {
            $result_str .= "<a href='./deeds_details?id={$row['idGuteTat']}' class='deedAnchor'><div class='deed'>";
            $result_str .= "<div class='name'><h4>" . $row['name'] . "</h4></div><div class='category'>" . $row['category'] . "</div>";
            $result_str .= "<br><br><br><br><div class='description'>" . $row['description'] . "</div>";
            $result_str .= "<div class='address'>" . $row['street'] . "<br>" . $row['postalcode'] . " / " . $row['place'] . "</div>";
            $result_str .= "<div>Anzahl der Helfer: " . $row['countHelper'] . "</div><div class='trustLevel'>Minimaler Vertrauenslevel: " . $row['idTrust'] . "</div>";
            $result_str .= "<div>Organisation: " . $row['organization'] . "</div>";
            $result_str .= "</div></a>";
            $result_str .= "<br><br>";
        }
        echo $result_str;


//-------------------  set free  -----------------


        mysqli_free_result($result);
        mysqli_free_result($res);


//pages
//echo "<a href='search.php?page=1&stichwort=".$_GET['stichwort']."&selector=".$_GET['selector']."'style='margin-left:30px;font-size:20px'>first</a> ";
//echo "<a href='search.php?page=".($page-1)."&stichwort=".$_GET['stichwort']."&selector=".$_GET['selector']."' style='margin-left:30px;font-size:20px'>previous</a>";
//echo "<a href='search.php?page=".($page+1)."&stichwort=".$_GET['stichwort']."&selector=".$_GET['selector']."' style='margin-left:30px;font-size:20px'>next</a>";
//echo "<a href='search.php?page={$maxpage}&stichwort=".$_GET['stichwort']."&selector=".$_GET['selector']."' style='margin-left:30px;font-size:20px'>last</a>";
//set page URLs

        echo setPageUrl(1, 'first');
        echo setPageUrl($page - 1, 'previous'); ?>

        <select id="page_selector" style="margin-left: 30px;" onchange="goto(this)">
            <option></option>
        </select>



<!--------------  create options  ---------------->


        <script type="text/javascript">
            var page_selector = document.getElementById('page_selector');
            for (var i = 1; i <=<?=$maxpage?>; i++) {
                var obj = document.createElement("option");
                obj.innerHTML = i;
                obj.value = i;
                if(i == <?=$page?>)
                    obj.selected="true";
                page_selector.appendChild(obj);
            }

            function goto(event) {
                var url="search.php?page="+event.value+"&stichwort=<?=$_GET['stichwort']?>&selector=<?=$_GET['selector']?>";
                window.open(url,'_self');
            }
        </script>


        <?php echo setPageUrl($page + 1, 'next');
        echo setPageUrl($maxpage, 'last');
        echo '<span style="position:relative;left:18%;font-size: 15px ">current:' . $page . ' of ' . $maxpage . '</span>';
        DBFunctions::db_close($db);
//$js_selector = '<script type="text/javascript">';
//$js_selector .= 'var selector= document.getElementsByName("selector");';
//$js_selector .= 'selector.options["ort"].selected=true;';
//$js_selector .= '</script>';
//echo $js_selector;
    }
}


//this function will be moved to file includes/ after all job been done


function setPageUrl($page, $sort)
{
    $str = "<a href='search.php?page=".$page.'&stichwort='.$_GET['stichwort'].'&selector='.$_GET['selector'].
        "' style='margin-left:30px;font-size:20px'>".$sort.'</a>';
    return $str;
}

?>

<?php require './includes/_bottom.php'; ?>
