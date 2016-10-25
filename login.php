<?php
/*
*@author Henrik Huckauf
*/

require './includes/_top.php';
?>

<h2><?php echo $wlang['login_head']; ?></h2>

<form action="">
	<input type="text" value="" placeholder="<?php echo $wlang['login_placeholder_username']; ?>">
	<br><br>
	<input type="password" value="" placeholder="<?php echo $wlang['login_placeholder_password']; ?>">
	<br><br>
	<input type="submit" value="<?php echo $wlang['login_button_submit']; ?>">
</form>

<?php
require './includes/_bottom.php';
?>