<?php
/*
*@author Henrik Huckauf
*/

session_start();

$hostname = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['PHP_SELF']);

if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true)
{
	$continue = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $hostname . '' . $_SERVER['REQUEST_URI'];
	$to = '/login?continue=' . $continue;
	header('Location: http://' . $hostname . ($path == '/' ? '' : $path) . $to);
	exit;
}
?>