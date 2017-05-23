<?php
/**
 * Detailseite
 *
 * Zeigt vollständige Informationen zu einer Tat
 *
 * @author Christian Hock <Christian.Hock@stud.hs-hannover.de>
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 * @author Shanghui Dai <shanghui.dai@stud.hs-hannover.de>
 */

require './includes/DEF.php';

include './includes/ACCESS.php';

require './includes/UTILS.php';

include './includes/comment.php';
require './includes/_top.php';

include "./includes/Map.php";
//Weiterleitung bei Fehlender ID
if(!isset($_GET['id']) || $_GET['id'] == "")
	$_USER->redirect("./deeds");
//Fehlermeldung bei falscher ID
$id = $_GET['id'];
$deed = DBFunctions::db_getGuteTat($id);
if($deed == null)
	echo '<red>Es konnte keine Tat zu der ID gefunden werden.</red><br><br><a href="./deeds"><input type="button" value="zur Übersicht" /></a>';
else
{
	//Setzen eines Standartbildes bei fehledndem Bild
	if($deed['pictures'] == "")
		$deed['pictures'] = './img/profiles/standard_other.png';
	
	echo '<h2>' . $deed['name'] . '</h2>';
	//Datehandler für die Zeiten
	$dh_start = (new DateHandler())->set($deed['starttime']);
	$dh_end = (new DateHandler())->set($deed['endtime']);

	$username = DBFunctions::db_getUsernameOfContactPersonByGuteTatID($id);
	$user = DBFunctions::db_get_user($deed['username']);
	$userid = $user['idUser'];
	$usermail = $user['email'];
	$rating = DBFunctions::db_getAvgRatingByGuteTatName($deed['name']);
	$star = $rating['rating'] / 5*100;
	if(isset($_POST['test'])){
		$rat = $_POST['rating'];
		DBFunctions::db_userEvaluateGuteTat($_USER->getID(),$id,$rat);
	}

	//Organisationsfeld wird nicht angezeigt, wenn es keine Organisation gibt
	$hasOrganization = $deed['organization'] != '';
	
	$map = new Map();
	if (!isset($_GET['user'])) $_GET['user'] = $_USER->getUsername();
	$thisuser = DBFunctions::db_get_user($_GET['user']);
	$userHouseNumber = isset($_POST['txtHausnummer']) ? $_POST['txtHausnummer'] : $thisuser['housenumber'];
	$userStreet = isset($_POST['txtStrasse']) ? $_POST['txtStrasse'] : $thisuser['street'];
	$userPostalcode = isset($_POST['txtPostalcode']) ? $_POST['txtPostalcode'] : $thisuser['postalcode'];
	
	echo '
	<div class="center">
		<img src="' . $deed['pictures'] . '" class="block" /><br>
		<div class="details">
			<div class="author"><a href="' . $HOST . '/profile?user=' . $username . '"><img src="' . $_USER->getProfileImagePathOf($userid, 32) . '" />' . $username . '</a></div>
			<div class="period">'.'vom: <span>' . $dh_start->get('d.m.Y ') . '</span> um <span>' . $dh_start->get('H:i') . '</span> Uhr' . '<br>bis: <span>' . $dh_end->get('d.m.Y ') . '</span> um <span>' . $dh_end->get('H:i') . '</span> Uhr</div>
			<div class="description"><span>' . $deed['name'] . '</span><br><br>' . $deed['description'] . '</div>';
			//Tableblock mit Informationen um die Tat herum
			echo '
			<div class="infos block">
				<table class="block">
					<tbody>
						<tr>
							<td class="infoLabel">Kategorie:</td>
							<td class="infoValue">' . $deed['categoryname'] . '</td>
							' . ($hasOrganization ?
							'<td class="infoLabel">Organisation:</td>
							<td class="infoValue">' . $deed['organization'] . '</td>'
							: '') . '
						</tr>
						<tr>
							<td class="infoLabel">Vertrauenslevel:</td>
							<td class="infoValue">' . $deed['idTrust'] . '</td>
							<td class="infoLabel">Benötigte&nbsp;Helfer:</td>
							<td class="infoValue">' . $deed['countHelper'] . '</td>
						</tr>
						<tr>
						<td class="infoLabel">Bewertung: </td>
						<td class="infoValue"> 
						<div class="star-rating">
        					<div class="star-rating-top" style="width:'.$star.'%">
            					<span>★</span>
            					<span>★</span>
            					<span>★</span>
            					<span>★</span>
            					<span>★</span>
        					</div>
        					<div class="star-rating-bottom">
            					<span>★</span>
            					<span>★</span>
            					<span>★</span>
            					<span>★</span>
            					<span>★</span>
        					</div>
    						</div> 
    						<span> Avg.:'.number_format($rating['rating'], 1).' &nbsp;&nbsp;/  &nbsp;&nbsp;'.$rating['people'].' Leute </span>
        				</td>
						</tr>
						<tr>
							<td class="infoLabel">Adresse:</td>
							<td rowspan="3" class="addressValue">' . $deed['street'] . '&nbsp;' . $deed['housenumber'] . ', ' . $deed['postalcode'] . '&nbsp;' . $deed['place'] . '</td>
							<td class="infoLabel">Entfernung:</td>
							<td class="infoValue">' . $map->getDistance($userPostalcode . ',' . $userStreet . ',' . $userHouseNumber, $deed['postalcode'] . ',' . $deed['street'] . ',' . $deed['housenumber']) . ' m' .'</td>
						</tr>
					<tbody>
				</table>
			</div>';
			
			
			$map->createSpace('9%', '350px', '82%');
			$map->createMap($deed['postalcode'] . ',' . $deed['street'] . ',' . $deed['housenumber']);
			
			echo '
		</div>
	</div>';

	$isCreator = $_USER->getUsername() == $deed['username'];
	if($isCreator)
	{
		echo '<a href="./deeds_bearbeiten?id=' . $id . '"><input type="submit" value="Bearbeiten"></a>
		<a href="./deeds_bewerten?id=' . $id . '"><input type="submit" value="Schließen"></a>
		<a href="./deeds_bewertung_taten?id=' . $id . '"><input type="submit" value="TatBewerbung"></a>';
		
		/*echo '<form method="post" action=""> // wenn Nutzer ihre Tat entfernen wollen soll sie geschlossen werden
			<input type="hidden" name="delete" value="false">
			<input type="submit" value="Löschen">
		</form>';*/
	}
	else if(DBFunctions::db_isUserCandidateOfGuteTat($id, $_USER->getID()) && empty(DBFunctions::isUserEvaluated($_USER->getUsername(),$deed['name']))){
		echo '<div class="bewertung-div">Geben Sie ihre Bewertung <br>(Achtung:Die Bewertung ist unveränderbar)</div>';
        $form = '<form action="" method="post">';
        $form .='<select name = "rating">';
		$form .= '<option value="1">1</option> <option value="2">2</option> <option value="3">3</option>';
		$form .= '<option value="4">4</option> <option value="5">5</option> <option value="0">Keine Bewertung</option>';
		$form .= '</select>';
        $form .='<input type="hidden" value="set" name="test">';
        $form .='<input type="submit" value="absenden" onclick="alert1()"> </form> <br> <hr>';
        echo $form;
        echo ' 
        <script type="text/javascript">
        	function alert1(){  
   				 alert("Sie haben shocn bewertet.");
			}  
		</script>
        ';
    }else if(!empty(DBFunctions::isUserEvaluated($_USER->getUsername(),$deed['name']))){
		echo '<div class="bewertung-div">Sie haben schon mit '.DBFunctions::isUserEvaluated($_USER->getUsername(),$deed['name'])['rating']." Star(s) bewertet.
			</div><hr>";
	}else
    {
		echo '<a href="./deeds_bewerbung?idGuteTat=' . $id . '"><input type="submit" value="Bewerben"></a>';
	}

	
	
	echo "<br><br><br><br>" . $_COMMENTS;
}

require './includes/_bottom.php';
?>

