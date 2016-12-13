<?php
//Include FB config file
require './includes/fb/fbConfig.php';

//Remove App permissions
$fbUid = $_SESSION['userData']['oauth_uid'];
$facebook->api('/'.$fbUid.'/permissions','DELETE');

//Unset user data from session
unset($_SESSION['userData']);

//Destroy session data
$facebook->destroySession();

unset($_COOKIE['fb_id']);
unset($_COOKIE['fb_email']);
unset($_COOKIE['fb_first_name']);
unset($_COOKIE['fb_last_name']);
unset($_COOKIE['fb_gender']);
unset($_COOKIE['fb_picture']);
unset($_COOKIE['fb_link']);

//Redirect to homepage
header("Location:index.php");
?>