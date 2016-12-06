<?php
/**
 * Index
 * Startseite, die für eingeloggte Nutzer einen Schnellzugriff zu wichtigen Funktionen bietet
 *
 * @author Lukas Buttke FTW
 */

require './includes/DEF.php';
require './includes/_top.php';
include './includes/db_connector.php';
//Include FB config file
require './fb/fbConfig.php';

	if(!$fbUser){
	    $fbUser = NULL;
	    $loginURL = $facebook->getLoginUrl(array('redirect_uri'=>$redirectURL,'scope'=>$fbPermissions));
	    $output = '<a href="'.$loginURL.'"><img src="./fb/images/fblogin-btn.png"></a>';     
	}else{
		$output = '<br/>Logged in with : Facebook';
	    $output .= '<br/>Logout from <a href="./fb/logout.php"><img src="./fb/images/fblogout-btn.png"></a>';
	}

?>

<h2><?php echo $wlang['welcome']; ?></h2>
<?php
	$messages = array( // später aus Datenbank
		'TueGutes: Soziale Hilfe aus der Nachbarschaft.',
		'TueGutes: Das soziale Netzwerk für Gutes.',
		'Verbessere deine Stadt und TueGutes.',
		'Hier wird dir geholfen: TueGutes.',
		'TueGutes: Wir verbinden Menschen.',
		'Deine Stadt, deine Taten!',
		'Mit jeder Tat ein Schritt zum Glück.',
		'You are Hannover!',
		'Make Hannover great again!'
	);
	$moveCount = mt_rand(0, sizeof($messages)-1);
	for($i = 0; $i < $moveCount; $i++)
		array_push($messages, array_shift($messages));
	
	$hideInlineCSS = ' class="hide"';
	echo '<div id="msg">';
	for($i = 0; $i < sizeof($messages); $i++)
		echo '<h3' . ($i != 0 ? $hideInlineCSS : '') . '><div>' . $messages[$i] . '</div></h3>';
	echo '</div>';
	
	echo "<script>
		var domEl = 'h3';
		$('#msg ' + domEl + ':gt(0)').hide(); // .not(':eq(randomNumber)')
		setInterval(function() {
			$('#msg ' + domEl + ':first-child').fadeOut(1000).next(domEl).fadeIn(1000).end().appendTo('#msg');
		}, 4800);
	</script>";
?>
<br>

<?php
if(!$_USER->loggedIn())
	echo "
		<div class='module transparent'>
			<br><br>
			<form action='./login' method='post'>
				<input type='text' value='' name='username' placeholder='" . $wlang['login_placeholder_username'] . "' required autofocus />
				<br><br>
				<input type='password' name='password' value='' placeholder='" . $wlang['login_placeholder_password'] . "' required />
				<br><br>
				<input type='submit' value='" . $wlang['login_button_submit'] . "' />
			</form>
			<br><br>
			<a href='./PasswortUpdate'>Ich habe mein Passwort vergessen!</a>
			<br><br>
			Ich bin noch nicht registriert:<br>
			<a href='./registration'>Zur Registrierung</a>
			<hr> 
			<div class='block'> <h4> oder mit Facebook einloggen: </h> <br> ".$output." </div>

		</div>
		<div class='module'>
			<br>
			Willkommen auf der Plattform für gute Taten im Raum Hannover.<br>
			<br>
			Hier kannst Du nach Deiner Anmeldung eine Liste von ausgeschriebenen guten Taten in Deiner Stadt einsehen 
			und Dich mit anderen guten Menschen darum bewerben, sie auszuführen.<br>
			<br>
			Dabei geht es nicht um Spenden, sondern um direkte Hilfe von Mensch zu Mensch oder Mensch zu Umwelt.
			<br>
			Viel Spaß!
			<br><br><br><br>
			<a href='./about'>Weitere Informationen</a>
			<br><br>
		</div>
	";
else
{
	echo "<a href='./deeds_create'><input type='button' value='Gute Tat erstellen' /></a><br>";

	echo "
		<div class='module'>
			<h3>Meine letzten Taten</h3><a href='./deeds?user=" . $_USER->getUsername() . "'><input type='button' value='Mehr' /></a>
			<div class='output'>";
			$id = $_USER->getID();
			$arr = DBFunctions::db_getGuteTatenForUser(0, 5, 'alle', $id);
			$maxZeichenFürDieKurzbeschreibung = 150;
			for($i = 0; $i < sizeof($arr); $i++){
				echo "<a href='./deeds_details?id=" . $arr[$i]->idGuteTat . "' class='deedAnchor'><div class='deed" . ($arr[$i]->status == "geschlossen" ? " closed" : "") . "'>";
					echo "<div class='name'><h4>" . $arr[$i]->name . "</h4></div><div class='category'>" . $arr[$i]->category . "</div>";
					echo "<br><br><br><br><div class='description'>" . (strlen($arr[$i]->description) > $maxZeichenFürDieKurzbeschreibung ? substr($arr[$i]->description, 0, $maxZeichenFürDieKurzbeschreibung) . "...<br>mehr" : $arr[$i]->description) . "</div>";
					echo "<div class='address'>" . $arr[$i]->street .  "  " . $arr[$i]->housenumber . "<br>" . $arr[$i]->postalcode . ' / ' . $arr[$i]->place . "</div>";
					echo "<div>" . (is_numeric($arr[$i]->countHelper) ? "Anzahl der Helfer: " . $arr[$i]->countHelper : '') ."</div><div class='trustLevel'>Minimaler Vertrauenslevel: " . $arr[$i]->idTrust . " (" . $arr[$i]->trustleveldescription . ")</div>";
					echo "<div>" . $arr[$i]->organization . "</div>";
				echo "</div></a>";
				echo "<br><br><hr><br>";
			}
	echo "
			</div>
		</div>";


	echo "
		<div class='module'>
			<h3>Die neusten Taten</h3><a href='./deeds'><input type='button' value='Mehr' /></a>
			<div class='output'>";
			$arr = DBFunctions::db_getGuteTatenForList(0, 5, 'freigegeben');
			$maxZeichenFürDieKurzbeschreibung = 150;
			for($i = 0; $i < sizeof($arr); $i++){
				echo "<a href='./deeds_details?id=" . $arr[$i]->idGuteTat . "' class='deedAnchor'><div class='deed" . ($arr[$i]->status == "geschlossen" ? " closed" : "") . "'>";
					echo "<div class='name'><h4>" . $arr[$i]->name . "</h4></div><div class='category'>" . $arr[$i]->category . "</div>";
					echo "<br><br><br><br><div class='description'>" . (strlen($arr[$i]->description) > $maxZeichenFürDieKurzbeschreibung ? substr($arr[$i]->description, 0, $maxZeichenFürDieKurzbeschreibung) . "...<br>mehr" : $arr[$i]->description) . "</div>";
					echo "<div class='address'>" . $arr[$i]->street .  "  " . $arr[$i]->housenumber . "<br>" . $arr[$i]->postalcode . ' / ' . $arr[$i]->place . "</div>";
					echo "<div>" . (is_numeric($arr[$i]->countHelper) ? "Anzahl der Helfer: " . $arr[$i]->countHelper : '') ."</div><div class='trustLevel'>Minimaler Vertrauenslevel: " . $arr[$i]->idTrust . " (" . $arr[$i]->trustleveldescription . ")</div>";
					echo "<div>" . $arr[$i]->organization . "</div>";
				echo "</div></a>";
				echo "<br><br><hr><br>";
			}
	echo "
			</div>
		</div>
		<br><br>
		<div class='module'>
			<h3>Nach Taten suchen</h3>
			<form action='./search' method='get'>
				<span></span>
				<input type='text' name='stichwort'>
				<select class='' name='selector' onchange='setTimeLabel(this)'>
					<option value='gutes'>Gutes</option>
					<option value='user_name'>User</option>
					<option value='ort'>Ort</option>
					<option value='zeit'>Zeit</option>
				</select>
				<input type='submit' name='sub' value='suchen' />
			</form>
		</div>
		<br><br><br><br>
		<a href='./profile'><input type='button' value='Mein Profil' /></a><br>
		<br><br><br><br>
		<a href='./contact'><input type='button' value='Kontakt zu uns' /></a>
	";
}
echo "
<script type='text/javascript'>
    function setTimeLabel(event) {
        var form = document.getElementById('form');
        var keyword = document.getElementsByName('stichwort');
        var origin = document.createElement('input');
        if (event.value == 'zeit') {
            var time = document.createElement('input');
            var format = getFormat();
            time.type = 'datetime-local';
            time.name = 'stichwort';
            time.value = format;
            keyword[0].parentNode.replaceChild(time,keyword[0]);

        }else{
            var origin = document.createElement('input');
            origin.type = 'text';
            origin.name = 'stichwort';
            keyword[0].parentNode.replaceChild(origin,keyword[0]);
        }
    }

    function getFormat(){
        var format = '';
        var nTime = new Date();
        format += nTime.getFullYear()+'-';
        format += (nTime.getMonth()+1)<10?'0'+(nTime.getMonth()+1):(nTime.getMonth()+1);
        format += '-';
        format += nTime.getDate()<10?'0'+(nTime.getDate()):(nTime.getDate());
        format += 'T';
        format += nTime.getHours()<10?'0'+(nTime.getHours()):(nTime.getHours());
        format += ':';
        format += nTime.getMinutes()<10?'0'+(nTime.getMinutes()):(nTime.getMinutes());
        format += ':00';
        return format;
    }

</script>
";








?>


<!--
<div class='center'>
	<a href='/login'>(Link) zum Login</a>
	<br><br>
	<input type="submit" value="submit">
	<br><br>
	<input type="button" value="button">
	<br><br>
	<input type="text" value="" placeholder="NAME">
	<br><br>
	<input type="email" value="" placeholder="EMAIL">
	<br><br>
	<input type="password" value="" placeholder="PASSWORT">
	<br><br>
	<textarea cols="16"  rows="2" placeholder="TEXT"></textarea>
	<br><br>
	<select>
		<option value="none">Bitte wählen</option>
		<option value="1">Option 1</option>
		<option value="2">Option 2</option>
		<option value="3">Option 3</option>
	</select>
	<br><br>
	<input id="radio1" name="radio" type="radio" checked="checked"><label for="radio1">Option 1</label>
	<br><br>
	<input id="radio2" name="radio" type="radio"><label for="radio2">Option 2</label>
	<br><br>
	<input id="radio3" name="radio" type="radio"><label for="radio3">Option 3</label>
	<br><br>
	<input id="checkbox1" type="checkbox" checked="checked"><label for="checkbox1">Check 1</label>
	&nbsp;
	<input id="checkbox2" type="checkbox"><label for="checkbox2">Check 2</label>
	&nbsp;
	<input id="checkbox3" type="checkbox"><label for="checkbox3">Check 3</label>
</div>
-->
<?php
require './includes/_bottom.php';
?>
