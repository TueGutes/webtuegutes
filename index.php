<?php
/*
*@author Henrik Huckauf
*/

require './includes/_top.php';
?>

<h2>Home</h2>
<h3>(und Beispielkram)</h3>
<br>
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

<?php
require './includes/_bottom.php';
?>