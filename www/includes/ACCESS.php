<?php
/**
 * Sichert Seiten
 *
 * Sichert Seiten vor dem Zugriff von Gastnutzern.
 * Diese Nutzer werden zur Loginseite weitergeleitet und nach erfolgreichem Login wieder zu ihrem eigentlichen Ziel
 *
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 */

//$path = dirname($_SERVER['PHP_SELF']);

if(!$_USER->loggedIn())
{
	$continue = $HOST_FULL;
	$to = '/login?code=423&continue=' . urlencode($continue);
	$_USER->redirect($HOST /*. ($path == '/' ? '' : $path)*/ . $to);
	exit;
}
?>