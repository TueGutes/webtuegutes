<?php
//Include FB config file && User class
require './includes/DEF.php';
require_once './includes/fb/fbConfig.php';
require './includes/_top.php';
require './includes/db_connector.php';

$tmp = "";
$out = "";
echo $tmp;

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

    setcookie("fb_id",$userData['oauth_uid'],(time()+86400*730),"/");
    setcookie("fb_email",$userData['email'],(time()+86400*730),"/");
    setcookie("fb_first_name",$userData['first_name'],(time()+86400*730),"/");
    setcookie("fb_last_name",$userData['last_name'],(time()+86400*730),"/");
    setcookie("fb_gender",$userData['gender'],(time()+86400*730),"/");
    setcookie("fb_picture",$userData['picture'],(time()+86400*730),"/");
    setcookie("fb_link",$userData['link'],(time()+86400*730),"/");


    $getUser = DBFunctions::db_getUserIDbyFacebookID($userData['oauth_uid']);

    if(!isset($_POST['username'])){

        $tmp = "kein Facebook";

    //Put user data into session
    $SESSION["userdata"] = $userData;
           
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
            }else{
                $output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
            }

            $out = '
                <div> '.$output.'</div>
                <hr> 
                <div class="username"> 
                   <form action="" method="post">
                       <b> Bitte einen Benutzernamen eingeben: </b> <br> 
                       <input type="text" name="username" placeholder="Username eingeben"> <br>
                      <input type="submit" value="Eintragen"> <br> 
                    </form>
                </div> ';

        }
    else{

        $tmp = "Facebook";
            $tmp = true;
            if(!$tmp){
                $loginData = DBFunctions::db_createOverFBBenutzerAccount($_POST['username'],$userData['oauth_uid'],$userData['first_name'],$userData['last_name'],$userData['email'],$userData['gender'],$userData['picture']);
            }
            else{
                $loginData = array(
                    'idUser'    => $getUser['user_id'],
                    'privacykey'    => $getUser['privacys']
                );

                echo 'Ausgabe der Datenbank-Facebook Daten: < Userid='.$getUser['user_id'].', PrivacyKey='.$getUser['privacys'].'>';
            }

            $login = array(
                'idUser'    => $loginData['idUser'],
                'username'     => $_POST['username'],
                'email'     => $userData['email'],
                'first_name'     => $userData['first_name'],
                'last_name'         => $userData['last_name'],
                'privacykey'         => $loginData['privacykey'],
                'gender'         => $userData['gender']
            );

            $_USER->login($login['idUser'], $login['username'], $login['email'], $login['first_name'], $login['last_name']);
            $_USER->set('privacykey', $login['privacykey']);
            $_USER->set('gender', $login['gender']);

            setcookie("fb_iduser",$login['idUser'],(time()+86400*730),"/");
            setcookie("fb_username",$login['username'],(time()+86400*730),"/");
            setcookie("fb_privacykey",$login['privacykey'],(time()+86400*730),"/");

            //setcookie("fblogin",$login,(time()+86400*730),"/")            

            $out = '<h3> Registration über Facebook hat geklappt !!! <br>';
            $out .= 'Dann noch viel Spaß auf unserer Seite ... <br> ';
            $out .= 'Über den Button gelangst du zu deiner Startseite: ';
            $out .= '<div class="eingeloggt"> 
                <form action="index.php" method="post">
                    <input type="submit" value="Ab Gehts! "> <br> 
                </form>
            </div>';

            // header("Location:./");

        }

   echo $getUser; 
   echo $out;

require './includes/_bottom.php';
?>