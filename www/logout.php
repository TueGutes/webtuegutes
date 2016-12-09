<?php
/**
 * Logout
 *
 * Loggt den aktuellen Benutzer aus
 *
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 */

include "./includes/DEF.php";

unset($_COOKIE['fb_iduser']);
unset($_COOKIE['fb_privacykey']);
$_USER->logout();
?>
