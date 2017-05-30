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
	if(isset($_GET['admin'])&& $_GET['admin']=='true' && $_USER->hasGroup($_GROUP_MODERATOR)){
	$admin = true ;
}else{
	$admin = false;
}
if($admin){
	echo'<h3> administrative Optionen </h3><br>';
	if(isset($_POST['adminAction'])){
		$action = $_POST['adminAction'];
		if($action == 'delete' && $_USER->hasGroup($_GROUP_ADMIN)){
			//echo'<red>Diese Tat wurde nun gelöscht.</red>';
			DBFunctions::db_deleteDeed($id);
		}else if($action =='close'){
			//echo'<red>Diese Tat wurde nun geschlossen.</red>';
			DBFunctions::db_guteTatAblehnen($id);
		}else if($action =='free'){
			//echo'<green> Diese Tat wurde nun freigegeben.</green>';
			DBFunctions::db_guteTatFreigeben($id);
		}else if($action =='deletePicture'){
			//echo'<red>Das Bild wurde nun entfernt.</red>';
			DBFunctions::db_update_deeds_picture("",$id);
		}else if($action =='editText'){
			//echo'<green>Die Beschreibung wurde nun geändert.</green>';
			DBFunctions::db_update_deeds_description($_POST['description'],$id);}
		$deed = DBFunctions::db_getGuteTat($id);
		}
	if($_USER->hasGroup($_GROUP_ADMIN)){
			echo'<span style="float:left;">
				<form method="post" action=""> 
					<input type="hidden" name="adminAction" value="delete">
					<a href="./deeds_details?id=' . $id . '&admin=true"><input type="submit" value="löschen"></a>
				</form>
			</span>';
	}
			echo' 
			<input type="hidden" name="adminAction" value="close">
			<a href="./deeds_details?id=' . $id . '&admin=true"><input type="submit" value="schließen"></a>
			</form>
			';
			echo'
			<span style="float:right;">
				<form method="post" action=""><form method="post" action=""> 
					<input type="hidden" name="adminAction" value="free">
					<a href="./deeds_details?id=' . $id . '&admin=true"><input type="submit" value="freigeben"></a>
				</form>
			</span>
			<br>
			<br>
			<br>';
}
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
	$map->dis = $map->getDistance($userPostalcode . ',' . $userStreet . ',' . $userHouseNumber, $deed['postalcode'] . ',' . $deed['street'] . ',' . $deed['housenumber']);
					if($map->dis == -1){
						$map->dis = "N/A";
					}else{
						$map->dis = $map->dis . " m";
					}
	echo '
	<body onLoad="noEdit()">
	<div class="center">
		<img src="' . $deed['pictures'] . '" class="block" /><br>
		<div class="details">
			<div class="author"><a href="' . $HOST . '/profile?user=' . $username . '"><img src="' . $_USER->getProfileImagePathOf($userid, 32) . '" />' . $username . '</a></div>
			<div class="period">'.'vom: <span>' . $dh_start->get('d.m.Y ') . '</span> um <span>' . $dh_start->get('H:i') . '</span> Uhr' . '<br>bis: <span>' . $dh_end->get('d.m.Y ') . '</span> um <span>' . $dh_end->get('H:i') . '</span> Uhr</div>
			';
			if($admin){
				echo'<div class="description" id="descriptionNoEdit"><span>' . $deed['name'] . '</span><br><br>' . $deed['description'] . '</div>
				<form method="post" action="" id ="send">
					<div class="description" id="descriptionEdit"><span><textarea name="description" cols="90" rows="6">'.$deed['description'] . '</textarea></div>
					<input type="hidden" name="adminAction" value="editText">
					<input type="submit" value="Bearbeiten" id = "button" OnClick="edit()">
					<br><br>
				</form>
				<center id = "noSend">
					<input type="submit" value="Bearbeiten" id = "button" OnClick="edit()">
					<br><br>
				</center>}';
			}else{
				echo'<div class="description" id="descriptionNoEdit"><span>' . $deed['name'] . '</span><br><br>' . $deed['description'] . '</div>';
			}
			
	if($admin){
		echo'
	<script>
		function edit() {
			if(document.getElementById("descriptionNoEdit").style.display=="block"){
				document.getElementById("descriptionNoEdit").style.display="none";
				document.getElementById("send").style.display="block";
				document.getElementById("noSend").style.display="none";
				document.getElementById("descriptionEdit").style.display="block";
				document.getElementById("button").value = "abschicken";
			}else{
				noEdit();
			}	
		}
		function noEdit() {
			document.getElementById("descriptionNoEdit").style.display="block";
			document.getElementById("send").style.display="none";
			document.getElementById("noSend").style.display="block";
			document.getElementById("descriptionEdit").style.display="none";
			document.getElementById("button").value = "ändern";
    
		}
	</script>';
	}
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
							<td class="infoValue">' . $map->dis .'</td>
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
echo '</body>';
?>

