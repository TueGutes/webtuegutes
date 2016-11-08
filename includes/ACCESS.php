<?php
/*
*@author Henrik Huckauf
*/

$path = dirname($_SERVER['PHP_SELF']);

if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true)
{
	$continue = $HOST . '' . $_SERVER['REQUEST_URI'];
	$to = 'login?continue=' . urlencode($continue);
	header('Location: ' . $HOST . ($path == '/' ? '' : $path) . $to);
	exit;
}
?>