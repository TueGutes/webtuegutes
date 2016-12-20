<?php
/**
 * Detailseite
 *
 * Zeigt vollständige Informationen zu einer Tat
 *
 * @author Christian Hock <Christian.Hock@stud.hs-hannover.de>
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 */

require './includes/DEF.php';

include './includes/ACCESS.php';

require './includes/UTILS.php';

include './includes/db_connector.php';

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
	//Organisationsfeld wird nicht angezeigt, wenn es keine Organisation gibt
	$hasOrganization = $deed['organization'] != '';
	echo '
	<div class="center">
		<img src="' . $deed['pictures'] . '" class="block" /><br>
		<div class="deed_details">
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
							<td class="infoLabel">Adresse:</td>
							<td rowspan="3" class="addressValue">' . $deed['street'] . '&nbsp;' . $deed['housenumber'] . ', ' . $deed['postalcode'] . '&nbsp;' . $deed['place'] . '</td>
						</tr>
					<tbody>
				</table>
			</div>';
			
			$map = new Map();
			$map->createSpace('9%', '350px', '82%');
			$map->createMap($deed['postalcode'] . ',' . $deed['street'] . ',' . $deed['housenumber']);
			
			echo '
		</div>
	</div>';

	$isCreator = $_USER->getUsername() == $deed['username'];
	if($isCreator)
	{
		echo '<a href="./deeds_bearbeiten?id=' . $id . '"><input type="submit" value="Bearbeiten"></a>
		<a href="./deeds_bewerten?id=' . $id . '"><input type="submit" value="Schließen"></a>';
		
		/*echo '<form method="post" action=""> // wenn Nutzer ihre Tat entfernen wollen soll sie geschlossen werden
			<input type="hidden" name="delete" value="false">
			<input type="submit" value="Löschen">
		</form>';*/
	}
	else
	{
		echo '<a href="./deeds_bewerbung?id=' . $id . '"><input type="submit" value="Bewerben"></a>';
	}
	
	
	echo "<br><br><br><br>" . $_COMMENTS;
}

require './includes/_bottom.php';
?>

