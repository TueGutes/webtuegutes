<?php
/*
*@author KLaus Sobotta, Lukas Buttke
*/

include './includes/ACCESS.php';
include './includes/db_connector.php';
require './includes/_top.php';
//require_once './guteTatAusgeben.php';
?>

<h2><?php echo $wlang['deeds_head']; ?> </h2>

<!-- Hier kann später mal gute Taten erstellen hervorkommen-->
<div class='ctop'>
<form action="guteTatErstellenHTML.php">
<input type="submit" value="Gute Tat erstellen" target="_self">
<br> <hr>
</div>
<br> 

		<?php
		
			/*$intZahl=0;
			$tatAusgeben=new tatAusgeben($intZahl);
			$tatAusgeben->toStringTat();
			*/
			//$allDeedsCount = db_connector blabla
			//$neededPages = $allDeedsCount/10;
			
			$arr = db_getGuteTatenForList(0, 10);
						
			for($i = 0; $i < sizeof($arr); $i++)
			{
				echo "<a href='./deeds_details?id=" . $arr[$i]->idGuteTat . "' style='display: inline-block; width: 80%;'><div class='deed' style='width: 100%; background: #aaaaaa; overflow: hidden;'>";
					echo "<div style='position: realtive; float: left;'><h4>" . $arr[$i]->name . "</h4></div><div style='position: realtive; float: right;'>" . $arr[$i]->category . "</div>";
					echo "<br><br><br><br><div style='position: realtive; text-align: left;'>" . (strlen($arr[$i]->description) > 8 ? substr($arr[$i]->description, 0, 8) . " mehr..." : $arr[$i]->description) . "</div>";
				echo "</div></a>";
				
				echo "<br><br><hr><br><br>";
			}
			
		?>

<br> <hr>	
<form action="" method="post">
<input type="submit" value="Nächste Seite">
<br> 


<?php
require './includes/_bottom.php';
?>
