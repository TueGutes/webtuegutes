<?php
/*
 *
 * @author Henrik Huckauf
 *
 */
function deleteDir($dirPath)
{
	if(!is_dir($dirPath))
		throw new InvalidArgumentException($dirPath . " ist kein Verzeichnis");
	if(substr($dirPath, strlen($dirPath) - 1, 1) != '/') // add / maybe
		$dirPath .= '/';
	$files = glob($dirPath . '{,.}[!.,!..]*', GLOB_MARK|GLOB_BRACE);
	foreach($files as $file)
	{
		if(is_dir($file))
			deleteDir($file);
		else
			unlink($file);
	}
	rmdir($dirPath);
}

$url = "http://www.vegas-baby.de/tueGutes/old_builds/";
$content = file_get_contents($url);

$content = explode('<tr><th colspan="5"><hr></th></tr>
</table>
<address>Apache/2.4.7 (Ubuntu) Server at www.vegas-baby.de Port 80</address>', $content)[0];
$content = explode('<tr><td valign="top"><img src="/icons/compressed.gif" alt="[   ]"></td><td><a href="', $content);
$content = $content[sizeof($content)-1];
$content = explode('"', $content)[0];
   
$localFN = '_DELETE_THIS.tar';
$localFNCompressed = $localFN . '.gz';
file_put_contents($localFNCompressed, fopen($url.$content, 'r'));
   
$arch = new PharData($localFNCompressed);  
$arch->decompress();
unset($arch);
Phar::unlinkArchive($localFNCompressed);

$localDN = '_DELETE_THIS';
$phar = new PharData($localFN);
$phar->extractTo($localDN);
unset($phar);
Phar::unlinkArchive($localFN);




$dump_path = $localDN . '/usr/local/tueGutes/www/';
$filename = 'db_dump.sql';

$mysql_host = 'localhost';
$mysql_username = 'tueGutes';
$mysql_password = 'Sadi23n2os';
$mysql_database = 'tuegutes';

$connection = mysqli_connect($mysql_host, $mysql_username, $mysql_password) or die('Fehler beim Verbinden zum MySQL server');
mysqli_select_db($connection, $mysql_database) or die('Datenbank konnte nicht ausgewählt werden: ' . mysqli_error($connection));

$lines = file($dump_path.$filename);
$templine = '';
foreach($lines as $line)
{
	if(substr($line, 0, 2) == '--' || $line == '') // skip comments
		continue;
	$templine .= $line;
	if(substr(trim($line), -1, 1) == ';') // end of query
	{
		mysqli_query($connection, $templine) or print('Fehler beim Ausführen von "<strong>' . $templine . '"</strong>: ' . mysqli_error($connection) . '<br><br>');
		$templine = '';
	}
}


deleteDir($localDN);

echo "Der aktuelle SQL Dump wurde erfolgreich in das lokale System importiert...<br>Dieser Tab kann nun geschlossen werden!";
?>