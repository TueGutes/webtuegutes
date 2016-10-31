<?php
/*
*@author Henrik Huckauf
*/

session_start();
//if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'])
	session_destroy();
header('Location: ./');
?>
