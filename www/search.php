<?php
/**
 * Search function
 * Durch "Zeit", "Ort", "Username", "Gutes"
 * "Zeit" in a different form, using "datetime-local"
 * @author     Shanghui Dai <shanghui.dai@stud.hs-hannover.de>
 */
require_once './includes/DEF.php';
require_once './includes/_top.php';
error_reporting(0);
date_default_timezone_set("Europe/Berlin");
?>

<!----------------  enter and input keyword  ----------------->



<form action="" method="get" id="form">
    <span class="keyword_text" name="keyword_text">
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


<!----------------- change Style when 'Zeit' ist selected  ------------------>


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
//$db = db_connect();
//$db = DBFunctions::db_connect();

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
    if($_GET['sort_selector']){
    $sort = $_GET['sort_selector'];
    }else{
        $_GET['sort_selector'] = 'status';
        $sort = 'status';
    }
    switch ($_GET['selector']){
        case 'gutes':
            $result = DBFunctions::db_searchDuringGutes($keyword,$sort);
            break;
        case 'user_name':
            $result = DBFunctions::db_searchDruingUsername($keyword,$sort);
            break;
        case 'ort':
            $result = DBFunctions::db_searchDuringOrt($keyword,$sort);
            break;
        case 'zeit':
            $result = DBFunctions::db_searchDuringZeit($keyword,$sort);
            break;
    }
    $num = mysqli_num_rows($result);
    $pagesize = 10;
    $maxpage = ceil($num / $pagesize);
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    if ($page < 1) {
        $page = 1;
    }
    if ($page > $maxpage) {
        $page = $maxpage;
    }


    //-------------- get one page ----------------

//
//    $limit = ' limit ' .(($page-1)*$pagesize).','.$pagesize;
//    $sql2 = $sql . " {$limit}";
//    $res = mysqli_query($db, $sql2);
//    $res = DBFunctions::db_searchSubresultGutes($keyword,$page,$pagesize);


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

    echo "<br><br><br><br><span class='resultSpan'>Suchergebnis:</span>";
    if ($num == 0) {
        echo "<br><br><hr><br><br><br><br>No Result";
    } else {
        echo "<span>Sortieren nach </span>";
        echo "<select id='sort_selector' onchange='goto(this)'>";
        echo "<option value='status' ";if($sort=='status'){echo 'selected';}echo ">Status</option>";
        echo "<option value='starttime' ";if($sort=='starttime'){echo 'selected';}echo ">Starttime</option>";
        echo "<option value='endtime' ";if($sort=='endtime'){echo 'selected';}echo ">Endtime</option>";
        echo "</select>";
        $result_str = "<br><br><hr><br><br>";
        for($i=1;$i<=$num;$i++){
            $row = mysqli_fetch_array($result);
            if(!$row) {
                break;
            }
//<div class='deed ({$row['status']}->status == 'geschlossen' ? ' closed' : ')>
            if($i>($page-1)*$pagesize && $i<=$page*$pagesize){
                $result_str .= "<a href='./deeds_details?id={$row['idGuteTat']}' class='deedAnchor'><div class='deed".($row['status'] == 'geschlossen' ? ' closed' : '')."'>";
                $result_str .= "<div class='name'><h4>" . $row['name'] . "</h4></div><div class='category'>" . $row['category'] . "</div>";
                $result_str .= "<br><br><br><br><div class='description'>" . $row['description'] . "</div>";
                $result_str .= "<div class='address'>" . $row['street'] . "<br>" . $row['postalcode'] . " / " . $row['place'] . "</div>";
                $result_str .= "<div>Anzahl der Helfer: " . $row['countHelper'] . "</div><div class='trustLevel'>Minimaler Vertrauenslevel: " . $row['idTrust'] . "</div>";
                $result_str .= "<div>Organisation: " . $row['organization'] . "</div>";
                $result_str .= "</div></a>";
                $result_str .= "<br><br>";
            }
        }
        echo $result_str;


//-------------------  set free  -----------------


        mysqli_free_result($result);


//pages
//echo "<a href='search.php?page=1&stichwort=".$_GET['stichwort']."&selector=".$_GET['selector']."'style='margin-left:30px;font-size:20px'>first</a> ";
//echo "<a href='search.php?page=".($page-1)."&stichwort=".$_GET['stichwort']."&selector=".$_GET['selector']."' style='margin-left:30px;font-size:20px'>previous</a>";
//echo "<a href='search.php?page=".($page+1)."&stichwort=".$_GET['stichwort']."&selector=".$_GET['selector']."' style='margin-left:30px;font-size:20px'>next</a>";
//echo "<a href='search.php?page={$maxpage}&stichwort=".$_GET['stichwort']."&selector=".$_GET['selector']."' style='margin-left:30px;font-size:20px'>last</a>";
//set page URLs

        echo setPageUrl(1, 'first');
        echo setPageUrl($page - 1, 'previous'); ?>

        <select id="page_selector" class="pageSelect" onchange="goto()">
            <option></option>
        </select>

<!--------------  create options  ---------------->


        <script type="text/javascript">
            var page_selector = document.getElementById('page_selector');
            var sort_selector = document.getElementById('sort_selector');
            for (var i = 1; i <=<?=$maxpage?>; i++) {
                var obj = document.createElement("option");
                obj.innerHTML = i;
                obj.value = i;
                if(i == <?=$page?>)
                    obj.selected="true";
                page_selector.appendChild(obj);
            }

            function goto() {
                var url="search.php?page="+page_selector.value+"&stichwort=<?=$_GET['stichwort']?>&selector=<?=$_GET['selector']?>&sort_selector="+sort_selector.value;
                window.open(url,'_self');
            }
        </script>


        <?php echo setPageUrl($page + 1, 'next');
        echo setPageUrl($maxpage, 'last');
        echo '<br>';
        echo '<span class="pageInfo">current:' . $page . ' of ' . $maxpage . '</span>';
//        db_close($db);
//$js_selector = '<script type="text/javascript">';
//$js_selector .= 'var selector= document.getElementsByName("selector");';
//$js_selector .= 'selector.options["ort"].selected=true;';
//$js_selector .= '</script>';
//echo $js_selector;
    }
}
//this function will be moved to file includes/ after all job been done
/**
 * set URL
 *
 * @param int $page the current page
 * @param String $sort the type of the search results
 * @return string $str return url in string form
 */
function setPageUrl($page, $name)
{
    $str = "<a href='search.php?page=".$page.'&stichwort='.$_GET['stichwort'].'&selector='.$_GET['selector'].'&sort_selector='.$_GET['sort_selector'].
        "' class='setPageAnchor'>".$name.'</a>';
    return $str;
}
?>
<?php require './includes/_bottom.php'; ?>
