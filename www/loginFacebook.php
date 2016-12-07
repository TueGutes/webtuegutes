<?php
//Include FB config file && User class
require './includes/DEF.php';
require_once './fb/fbConfig.php';
require './includes/_top.php';
require './includes/db_connector.php';

    //Get user profile data from facebook
    $fbUserProfile = $facebook->api('/me?fields=id,first_name,last_name,email,link,gender,locale,picture');
    
    //Insert or update user data to the database
    $userData = array(
        'oauth_provider'=> 'facebook',
        'oauth_uid'     => $fbUserProfile['id'],
        'first_name'     => $fbUserProfile['first_name'],
        'last_name'     => $fbUserProfile['last_name'],
        'email'         => $fbUserProfile['email'],
        'gender'         => $fbUserProfile['gender'],
        'locale'         => $fbUserProfile['locale'],
        'picture'         => $fbUserProfile['picture']['data']['url'],
        'link'             => $fbUserProfile['link']
    );
/*
if(getUserByFacebook($userData['oauth_uid']) != false){
    $loginData = DBFunctions::db_createOverFBBenutzerAccount($_POST['username'],$userData['oauth_uid'],$userData['first_name'],$userData['last_name'],$userData['email'],$userData['gender'],$userData['picture']);

    $_USER->login($loginData['idUser'], $_POST['username'], $userData['email'], $userData['first_name'], $userData['last_name']);
    $_USER->set('privacykey', $loginData['privacykey']);
    $_USER->set('gender', $userData['gender']);
}
else{
*/
    if(isset($_POST['username'])){
        $loginData = DBFunctions::db_createOverFBBenutzerAccount($_POST['username'],$userData['oauth_uid'],$userData['first_name'],$userData['last_name'],$userData['email'],$userData['gender'],$userData['picture']);

        $_USER->login($loginData['idUser'], $_POST['username'], $userData['email'], $userData['first_name'], $userData['last_name']);
        $_USER->set('privacykey', $loginData['privacykey']);
        $_USER->set('gender', $userData['gender']);
    }
    else{

         //Put user data into session
        $_SESSION['userData'] = $userData;
        
        //Render facebook profile data
        if(!empty($userData)){
            $output = '<h3> Ihre Facebook Profile Details: </h1>';
            $output .= '<img src="'.$userData['picture'].'">';
            $output .= '<br/>Facebook ID : ' . $userData['oauth_uid'];
            $output .= '<br/>Name : ' . $userData['first_name'].' '.$userData['last_name'];
            $output .= '<br/>Email : ' . $userData['email'];
            $output .= '<br/>Gender : ' . $userData['gender'];
            $output .= '<br/>Locale : ' . $userData['locale'];
            $output .= '<br/>Logged in with : Facebook';
            $output .= '<br/><a href="'.$userData['link'].'" target="_blank">Click to Visit Facebook Page</a>';
            $output .= '<br/>Logout from <a href="logout.php">Facebook</a>'; 
        }else{
            $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
        }
    }
/*    
} */
?>

<?php 
if(!isset($_POST['username'])){
	$out = '
	<div> '.$output.'</div>
	<hr> 
	<div class="username"> 
		<form action="" method="post">
			<b> Bitte einen Benutzernamen eingeben: </b> <br> 
			<input type="text" name="username" placeholder="Username eingeben"> <br>
			<input type="submit" value="Eintragen"> <br> 
		</form>
	</div>
	';
}

else{
	$out = '<h3> Registration über Facebook hat geklappt !!! <br>';
    $out .= 'Dann noch viel Spaß auf unserer Seite ... <br> ';
    $out .= 'Über den Button gelangst du zu deiner Startseite: ';
    $out .= '<div class="eingeloggt"> 
        <form action="index.php" method="post">
            <input type="submit" value="Ab Gehts! "> <br> 
        </form>
    </div>';

    //Redirect to homepage
    header("Location:../");
}

echo $out;
?>

<?php
require './includes/_bottom.php';
?>