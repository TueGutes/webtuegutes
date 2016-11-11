<?php
/*
*@author Henrik Huckauf
*/

//$path = dirname($_SERVER['PHP_SELF']);

if(!$_USER->loggedIn())
{
	$continue = $HOST;
	$to = '/login?code=423&continue=' . urlencode($continue);
	$_USER->redirect($HOST /*. ($path == '/' ? '' : $path)*/ . $to);
	exit;
}
?>