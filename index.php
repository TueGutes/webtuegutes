<?php
/*
*@author Henrik Huckauf
*/

require './includes/DEF.php';

require './includes/_top.php';
?>

<h2><?php echo $wlang['welcome']; ?></h2>
<br>

<?php 
if(!$_USER->loggedIn()) 
	echo "
		<form action='./login' method='post'>
			<input type='text' value='' name='username' placeholder='" . $wlang['login_placeholder_username'] . "' required />
			<br><br>
			<input type='password' name='password' value='' placeholder='" . $wlang['login_placeholder_password'] . "' required />
			<br><br>
			<input type='submit' value='" . $wlang['login_button_submit'] . "' />
		</form>
		<br><br>
		<a href='./PasswortUpdate'>Ich habe mein Passwort vergessen!</a>
		<br><br><br><br>
		Ich bin noch nicht registriert:<br>
		<a href='./registration'>Zur Registrierung</a> 
	";
else
{
	echo "<a href='./guteTatErstellenHTML'><input type='button' value='Gute Tat erstellen' /></a><br>";

	echo "
		<div class='module'>
			<h3>Meine letzten Taten</h3><a href='./deeds?user=" . $_USER->getUsername() . "'><input type='button' value='Mehr' /></a>
			<div class='output'>";
			for($i = 0; $i < 5; $i++)
			{
				echo "<a href='./deeds_details?id=x' class='deedAnchor'><div class='deed'>";
					echo "<div class='name'><h4>Test " . $i . "</h4></div><div class='category'>Test</div>";
					echo "<br><br><br><br><div class='description'>Dies ist eine Testbeschreibung, yo...</div>";
					echo "<div class='address'>Staße 42<br>1234 / Stadtteil</div>";
					echo "<div>Anzahl der Helfer: 2</div><div class='trustLevel'>Minimaler Vertrauenslevel: 42 (krasser Typ)</div>";
					echo "<div>Organisation</div>";
				echo "</div></a>";
				echo "<br><br>";
			}
	echo "
			</div>
		</div>";
		
		
	echo "
		<div class='module'>
			<h3>Die neusten Taten</h3><a href='./deeds'><input type='button' value='Mehr' /></a>
			<div class='output'>";
			for($i = 0; $i < 5; $i++)
			{
				echo "<a href='./deeds_details?id=x' class='deedAnchor'><div class='deed'>";
					echo "<div class='name'><h4>Test " . $i . "</h4></div><div class='category'>Test</div>";
					echo "<br><br><br><br><div class='description'>Dies ist eine Testbeschreibung, yo...</div>";
					echo "<div class='address'>Staße 42<br>1234 / Stadtteil</div>";
					echo "<div>Anzahl der Helfer: 2</div><div class='trustLevel'>Minimaler Vertrauenslevel: 42 (krasser Typ)</div>";
					echo "<div>Organisation</div>";
				echo "</div></a>";
				echo "<br><br>";
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
				<select class='' name='selector'>
					<option value='gutes'>Gutes</option>
					<option value='user_name'>User</option>
					<option value='ort'>Ort</option>
				</select>
				<input type='submit' name='sub' value='suchen' />
			</form>
		</div>
		<br><br><br><br>
		<br><br><br><br>
		<a href='./profile'><input type='button' value='Mein Profil' /></a><br>
		<br><br><br><br>
		<br><br><br><br>
		<a href='./contact'><input type='button' value='Kontakt zu uns' /></a>
	";
}
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