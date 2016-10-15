<?php
	include "script/session.php";
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
		<?php include "top.php";

		if ($_SESSION['user']==="null") {
			echo '<p><p><strong>Inhalte nur für eingeloggte Mitglieder sichtbar!<p>Jetzt <a href="registration.php">Mitglied werden</a>';
		} else {
			include "content/index.html";
		}

		?>
</html>
