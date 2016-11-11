<?php
/*
*@author Henrik Huckauf
*/

//====Server====
$ABSOLUE_PATH = "/";
$HOSTNAME = $_SERVER['HTTP_HOST'];
$HOST_FULL = "http" . (isset($_SERVER['HTTPS']) ? "s" : "") . "://" . $HOSTNAME . $_SERVER['REQUEST_URI'];
$HOST = substr($HOST_FULL, 0, strrpos($HOST_FULL, "/"));
$USE_GMAIL = false; // Bei true wird der gmail Account tuegutesinhannover@gmail.com von der PHPMailer Klasse verwendet um Mails zu senden. Bei false wird die PHP Funktion mail verwendet

//====Datenbank====
$DB_HOST = "localhost";
$DB_DATABASE = "tuegutes";
$DB_USER = "tuegutes";
$DB_PASSWORD = "password";

session_start();

include './includes/mail.php';

require './includes/user.php';
$_USER = new User();

require './includes/LANGUAGE.php';
?>