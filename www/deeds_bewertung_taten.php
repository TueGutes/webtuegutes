<?php
/*
*@author Shanghui Dai <shanghui.dai@stud.hs-hannover.de>
*/

require './includes/DEF.php';
include './includes/ACCESS.php';
require './includes/_top.php';
require './includes/UTILS.php';

$id = $_GET['id'];
$deed = DBFunctions::db_getGuteTat($id);
echo '<h3>  Hier sind die Bewertungen der guten Taten aus Users </h><br>';
if($deed == null)
    echo '<red>Es konnte keine Tat zu der ID gefunden werden.</red><br><br><a href="./deeds"><input type="button" value="zur Übersicht" /></a>';
else {
    $result = DBFunctions::db_getAllRatingByGuteTatName($deed['name']);
    $num = mysqli_num_rows($result);
    echo '
		<div class = "raring-table-div">
			<table class="rating-table" style="border=30px">
				<tbody>
					<tr>
						<th>User</th>
						<th>Rating</th>
						<th>Time</th>
					</tr>';
    for($i = 0; $i<$num;$i++){
        $row = mysqli_fetch_array($result);
    	echo '
			<tr class="rating-table-tr">
				<td>'.$row['username'].'</td>
				<td>'.$row['rating'].'</td>
				<td>'.$row['time'].'</td>
			</tr>
		';
    }
    echo'
				</tbody>
			</table>
		</div>
	';
}
echo '<hr><a href="./deeds_details?id='.$id.'"> <input type="Button" value="Zurück"> </a>';
?>

<?php
require './includes/_bottom.php';
?>