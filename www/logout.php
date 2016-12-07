<?php
/**
 * Logout
 *
 * Loggt den aktuellen Benutzer aus
 *
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 */

include "./includes/DEF.php";
//Include FB config file
require_once 'fbConfig.php';

$_USER->logout();

//Unset user data from session
unset($_SESSION['userData']);

//Destroy session data
$facebook->destroySession();

//Redirect to homepage
header("Location:../");
?>
