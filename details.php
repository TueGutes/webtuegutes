<?php
/*
*@author Lukas Buttke
*/

include './includes/ACCESS.php';
include './includes/db_connector.php';
require './includes/_top.php';

$idTat = 4;//$_GET["id"]; 
$tat = db_getGuteTat($idTat);
?>

<?php
// $blAbout = '<hr> <img src= "'.$tat["pictures"] .'<br>' ;
$blAbout = '<h2>'.$tat["name"] .'<br>';
$blAbout .= ' Gute Tat #'.$idTat.' </h>';

// $blSelf = 'Wurde erzeugt von: ';
$blSelf = '<br> <img src="' . $tat["avatar"] . '" width="25" height="25" >';
$blSelf .= $tat["username"];

$blComb = '<table> <tr> <td> '.$blSelf.'</td> <td> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp 
</td> <td>'.$blAbout.' </td> </tr> </table> <hr>';

$blTaten = '<table> <tr> <td> Categorie </td> <td>'.$tat["category"].'</td> </tr>';
$blTaten .= '<tr> <td> <br>Beschreibung: <br> '.$tat["description"].'</td> </tr>';
$blTaten .= '<tr> <td> <br>Zeitpunkt: </td> <td> <br>'.$tat["time"].'</td> </tr>';
$blTaten .= '<tr> <td> <br>Organisation: </td> <td> <br>'.$tat["organization"].'</td> </tr>';
$blTaten .= '<tr> <td> <br>Anzahl Helfer: </td> <td> <br>'.$tat["countHelper"].'</td> </tr> </table>';


$blMap = 'Map Anbindung '.'<hr>';
$blTrust = '<h4> benötigtes Vertrauenslevel '.$tat["name"].'<hr>';

echo '<div align="center">' . $blComb . '</div>';
echo '<p />';
echo '<div align="center"> <h6>' . $blTaten . '</h></div>';
echo '<p />';
echo '<div align="center">' . $blMap . '</div>';
echo '<p />';
echo '<div align="center">' . $blTrust . '</div>';
echo '<p />';

?>

<a href="tatBearbeiten.php" target="_self" > <input type="Button" value="Bearbeiten"> </a>
<a href="tatBearbeiten.php" target="_self" > <input type="Button" value="Bewerben"> </a>

<?php
require './includes/_bottom.php';
?>