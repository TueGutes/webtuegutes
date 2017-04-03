<?php
/**
 * Adminseite
 *
 * Gibt Übersicht über die verfügbaren Adminbereiche und verwaltet diese
 *
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 */

$page = @$_GET['page'];
if($page == 'user')
{
	require('./includes/admin/admin_user.php');
	exit;
}
else if($page == 'deeds')
{
	require('./includes/admin/admin_deeds.php');
	exit;
}
 
require './includes/DEF.php';

require './includes/_top.php';
?>

<h2>Administration</h2>

<div class="module transparent">
	<a href="./admin?page=deeds"><input type="button" value="Tatenverwaltung" /></a><br>
	<br>
	<a href="./admin?page=user"><input type="button" value="Nutzerverwaltung" /></a>
</div>

<?php
require './includes/_bottom.php';
?>