<?php
	include "script/session.php";

	DEFINE('MAX_PAGES',2);

	function getPage() {
		if (!(isset($_GET['page'])))
			return 1;
		else
			return $_GET['page'];
	}
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>TueGutes Landing Page</title>
		<meta charset="UTF-8">
		<style type="text/css">
			h1 {margin-left:25%;}
			h1 {margin-right:25%;}
			h2 {margin-left:25%;}
			h2 {margin-right:25%;}
			p {margin-left:25%;}
			p {margin-right:25%;}
		</style>
	</head>

	<body>
		<?php 

			include "top.php";

			if ($_SESSION['user']==="null") {
				echo '<p><p><strong>Inhalte nur für eingeloggte Mitglieder sichtbar!<p>Jetzt <a href="registration.php">Mitglied werden</a>';
			} else {

				if (@$_GET['page']=="2") {
					include "content/index2.html";
				} else {
					if (isset($_GET['page']))
						header('Location: ./');
					include "content/index.html";
				}

			}

			echo '<div style="margin-left:25%;margin-right:25%;background-color:#757575">';
				if (isset($_GET['page']) && $_GET['page']!=1)
					echo '<div style="text-align:left"><a href="index.php?page='.($_GET['page']-1).'"><--</a></div>';	
				if (@$_GET['page']<MAX_PAGES)
					echo '<div style="text-align:right"><a href=index.php?page=' . ((isset($_GET['page'])?$_GET['page']:1)+1) . '>--></a>';
			echo '</div>';

		?>
</html>
