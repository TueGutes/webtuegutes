<?php
/**
 * Oberer Teil der Website
 *
 * Enthält den oberen Teil der Website (z.B. das Menü)
 *
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 */

$currentPath = $_SERVER['PHP_SELF'];
$extension = '.php';
$activeTab = 'class="active"';
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
    <title>TueGutes</title>
	<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico" />
	<?php /*<meta name="author" content="Henrik Huckauf" />*/ ?>
    <!--====responsive viewport====-->
    <meta name="viewport" content="width = device-width, initial-scale = 1, maximum-scale = 1">
	<!--====/responsive viewport====-->
    <link rel="stylesheet" href="styles/theme.css" type="text/css" />
    <link rel="stylesheet" href="styles/responsive.css" type="text/css" />
	<!--====for mobile icons====--><link rel="stylesheet" href="styles/font-awesome.min.css" type="text/css" /><!--====/for mobile icons====-->
	
	<link rel="stylesheet" href="js/jQueryUI/jquery-ui.min.css">
	<script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
	<script type="text/javascript" src="js/jQueryUI/jquery-ui.min.js"></script>

	<script type="text/javascript" src="js/modernizr.js"></script>
	
	<script type="text/javascript" src="js/global.js"></script>
	
	<script type="text/javascript" src="js/facebook.js"></script>
</head>

<body>
<div class="holder">
	<div class="wrap">
		<header>
			<div class="center">
				<div class="siteLogo">
					<h1><a href="./"><img src="./img/wLogo.png<?php /*https://www.google.de/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png*/ ?>" /><!--TueGutes--></a></h1>
				</div>
				<div id="profileSection">
					<?php
					$profileMenuLinks = '
						<a href="./profile">Mein Profil</a>
						<a href="./deeds_create">Tat erstellen</a>
						<a href="./logout">Logout</a>
					';
					if($_USER->loggedIn())
						echo '<div class="profile"><img src="' . $_USER->getProfileImagePath(64) . '" class="block" /><br>' . $_USER->getUsername() . '</div>
						<div class="profileContent">
							' . $profileMenuLinks . '
						</div>';
					else
						echo '<div class="profile guest"><a href="./login">' . $wlang['login_head'] . '</a><br><a href="./registration">' . $wlang['register_head'] . '</a></div>';
					?>
				</div>
				<div id="mobileSection">
					<div class="mobile">
						<?php
						echo !$_USER->loggedIn() ? '<a href="./login"><i class="fa fa-sign-in"></i></a>' : '
						<i class="profile"><img src="' . $_USER->getProfileImagePath(64) . '" class="block" /></i>
						<div class="mobileProfileContent closed">
							' . $profileMenuLinks . '
						</div>';
						?>
						<i class="fa fa-bars mobileMenuButton"></i><i class="fa fa-times mobileMenuButton"></i>
					</div>
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