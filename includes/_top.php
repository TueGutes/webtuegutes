<?php
	require_once './includes/DEF.php';
	require_once './includes/LANGUAGE.php';
	
	$currentPath = $_SERVER['PHP_SELF'];
	$extension = '.php';
	$activeTab = 'class="active"';
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
    <title>TueGutes</title>
	<meta name="author" content="Henrik Huckauf" />
    <!--====responsive viewport====-->
    <meta name="viewport" content="width = device-width, initial-scale = 1, maximum-scale = 1">
	<!--====/responsive viewport====-->
    <link rel="stylesheet" href="styles/theme.css" type="text/css" />
    <link rel="stylesheet" href="styles/responsive.css" type="text/css" />
	<!--====for mobile icons====--><link rel="stylesheet" href="styles/font-awesome.min.css" type="text/css" /><!--====/for mobile icons====-->
	
	<script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
	<script type="text/javascript" src="js/global.js"></script>
</head>

<body>
<div class="holder">
	<div class="wrap">
		<header>
			<div class="center">
				<div class="siteLogo">
					<h1><a href="./"><img src="./img/wLogo.png<?php /*https://www.google.de/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png*/ ?>" /><!--TueGutes--></a></h1>
				</div>
				<?php
					if($_SESSION['loggedIn'] === true) {
						
						echo '<div id="quickLogin"> <form action="login.php" method="post"> <input type="submit" value="Log out"/></form></div>';
						
					}
					else {
						$placeholderUsername = $wlang['login_placeholder_username'];
						$placeholderPassword = $wlang['login_placeholder_password'];
						$placeholderButton = $wlang['login_button_submit'];						
						
						echo '<div id="quickLogin">
							<form action="login.php" method="post">
								<input type="text" value="" name="username" placeholder='.$placeholderUsername.' />
								<input type="password" name="password" value="" 
								placeholder='.$placeholderPassword.' />
								<br>
								<input type="submit" value='.$placeholderButton.' />
							</form>
						</div>';
						
					}
				
				
				?>
				
				<div id="mobileSection">
					<div class="mobile"><i class="fa fa-bars"></i><i class="fa fa-times"></i></div>
					<div class="mobileMenu">
						<nav class="navigation">
							<ul>
								<li <?php echo $currentPath==$ABSOLUE_PATH.'index'.$extension?$activeTab:''; ?>>                                
									<a href="./"><?php echo $wlang['nav_home']; ?></a>
									<span class="menuItemBG"></span>
								</li>
								<li <?php echo $currentPath==$ABSOLUE_PATH.'about'.$extension?$activeTab:''; ?>>
									<a href="./about"><?php echo $wlang['nav_about']; ?></a>
									<span class="menuItemBG"></span>
								</li>
								<li <?php echo $currentPath==$ABSOLUE_PATH.'deeds'.$extension?$activeTab:''; ?>>
									<a href="./deeds"><?php echo $wlang['nav_deeds']; ?></a>
									<span class="menuItemBG"></span>
								</li>
								<li <?php echo $currentPath==$ABSOLUE_PATH.'contact'.$extension?$activeTab:''; ?>>
									<a href="./contact"><?php echo $wlang['nav_contact']; ?></a>
									<span class="menuItemBG"></span>
								</li>
							</ul>
						</nav>
					</div>
				</div>
				
				<div class="clear"></div>
			</div>
		</header>

		<div id="container">
		
		
			<div class="content">
