<?php
/*
*@author Andreas Blech
*/

$placeholderUsername = $wlang['login_placeholder_username'];
$placeholderPassword = $wlang['login_placeholder_password'];
$placeholderButton = $wlang['login_button_submit'];

echo '<form action="login.php" method="post" >
	<input type="text" value="" name="username" placeholder = '.$placeholderUsername.'>
	<br><br>
	<input type="password" name="password" value="" placeholder='.$placeholderPassword.'>
	<br><br>
	<input type="submit" value='.$placeholderButton.' >
	</form>';
		
echo'<a href="PasswortUpdate.php">Passwort vergessen?</a>';
		
?>