<?php
//Include FB config file && User class
require './includes/DEF.php';
require_once './includes/fb/fbConfig.php';
require './includes/_top.php';
require './includes/db_connector.php';

//------------------------------ INITIALISIERUNGEN -----------------------------------------------------
    //----------------- Facebook --------------------------
    $out="";
    //Get user profile data from facebook
    $fbUserProfile = $facebook->api('/me?fields=id,first_name,last_name,email,link,gender,locale,picture.width(512).height(512)');
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
    //--------------------- Cookies -----------------------------------
    setcookie("fb_id",$userData['oauth_uid'],(time()+86400*730),"/");
    setcookie("fb_email",$userData['email'],(time()+86400*730),"/");
    setcookie("fb_first_name",$userData['first_name'],(time()+86400*730),"/");
    setcookie("fb_last_name",$userData['last_name'],(time()+86400*730),"/");
    setcookie("fb_gender",$userData['gender'],(time()+86400*730),"/");
    setcookie("fb_picture",$userData['picture'],(time()+86400*730),"/");
    setcookie("fb_link",$userData['link'],(time()+86400*730),"/");


    // -------------------------- Unsere Datenbank ----------------------------------------
    $getUser = DBFunctions::db_getUserIDbyFacebookID($userData['oauth_uid']);

//-------------------------- Kontroll Block unregistriert -------------------------------------------
if(!isset($getUser['user_id'])){
    if(!isset($_POST['username'])){
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

            //"Picture" wird nicht mehr mit übergeben, da Timm in der Datenbank den korrekten Pfad zu dem Profilbild auf unserem Server angibt
            $loginData = DBFunctions::db_createOverFBBenutzerAccount($_POST['username'],$userData['oauth_uid'],$userData['first_name'],$userData['last_name'],$userData['email'],$userData['gender']);

            $uploadDir = './img/profiles/'.$loginData['idUser'].'/';
            if (!is_dir('./img/profiles/')) {
                mkdir('./img/profiles/');
                chmod('./img/profiles/', 0775);
            }
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir);
                chmod($uploadDir, 0775);
            }
            imagepng(imagecreatefromstring(file_get_contents($userData['picture'])), $uploadDir . 'converted.png');
            $size = getimagesize($uploadDir . 'converted.png');

            //Anlegen der Dateien
            $uploaded = imagecreatefrompng($uploadDir . 'converted.png');
            $avatar_512 = imagecreatetruecolor(512,512);
            $avatar_256 = imagecreatetruecolor(256,256);
            $avatar_128 = imagecreatetruecolor(128,128);
            $avatar_64 = imagecreatetruecolor(64,64);
            $avatar_32 = imagecreatetruecolor(32,32);

            //Resizing
            imagecopyresized($avatar_512, $uploaded, 0, 0, 0, 0, 512, 512 , $size[0], $size[1]);
            imagecopyresized($avatar_256, $uploaded, 0, 0, 0, 0, 256, 256 , $size[0], $size[1]);
            imagecopyresized($avatar_128, $uploaded, 0, 0, 0, 0, 128, 128 , $size[0], $size[1]);
            imagecopyresized($avatar_64, $uploaded, 0, 0, 0, 0, 64, 64 , $size[0], $size[1]);
            imagecopyresized($avatar_32, $uploaded, 0, 0, 0, 0, 32, 32 , $size[0], $size[1]);

            imagepng($avatar_512, $uploadDir . '512x512.png');
            imagepng($avatar_256, $uploadDir . '256x256.png');
            imagepng($avatar_128, $uploadDir . '128x128.png');
            imagepng($avatar_64, $uploadDir . '64x64.png');
            imagepng($avatar_32, $uploadDir . '32x32.png');

            //chmod('./img/profiles/', 0775);
            chmod($uploadDir.'512x512.png', 0775);
            chmod($uploadDir.'256x256.png', 0775);
            chmod($uploadDir.'128x128.png', 0775);
            chmod($uploadDir.'64x64.png', 0775);
            chmod($uploadDir.'32x32.png', 0775);

            unlink($uploadDir . 'converted.png');

            //$thisuser['avatar'] = $uploadDir.'512x512.png';

            header("Location:./loginFacebook.php");

        }
}
//-------------------------- Kontroll Block einloggen -------------------------------------------
    else{
                   
            //------------------- User Anlegen, fals nicht existiert ------------------------

            if(!empty($getUser['user_id'])){
                $loginData = array(
                    'idUser'    => $getUser['user_id'],
                    'privacykey'    => $getUser['privacykey']
                );
                /* echo   'Ausgabe der Datenbank-Facebook Daten: 
                        < Userid='.$getUser['user_id'].', PrivacyKey='.$getUser['privacykey'].'>'; */               
            }
            else{ echo "<h2> Gut ! Account bereits vorhanden. Weiter zum Login. </h2> "; }            
            // ------------------------------- LoginDaten sammeln -------------------------------------

        if(!empty($_POST['username'])){
            $login = array(
                'idUser'    => $loginData['idUser'],
                'username'     => $_POST['username'],
                'email'     => $userData['email'],
                'first_name'     => $userData['first_name'],
                'last_name'         => $userData['last_name'],
                'privacykey'         => $loginData['privacykey'],
                'gender'         => $userData['gender']
            );
        }else{
            $username = DBFunctions::db_getUsernameOfBenutzerByID($loginData['idUser']);
            $login = array(
                'idUser'    => $loginData['idUser'],
                'username'     => $username,
                'email'     => $userData['email'],
                'first_name'     => $userData['first_name'],
                'last_name'         => $userData['last_name'],
                'privacykey'         => $loginData['privacykey'],
                'gender'         => $userData['gender']
            );
        }
            //------------------------------- Einloggen und Setzen der LoginCookies ---------------------

            $_USER->login($login['idUser'], $login['username'], $login['email'], $login['first_name'], $login['last_name']);
            $_USER->set('privacykey', $login['privacykey']);
            $_USER->set('gender', $login['gender']);

            setcookie("fb_iduser",$login['idUser'],(time()+86400*730),"/");
            setcookie("fb_username",$login['username'],(time()+86400*730),"/");
            setcookie("fb_privacykey",$login['privacykey'],(time()+86400*730),"/");

            // ----------------------------- Ausgabe, falls Weiterleitung versagt -----------------------------------
            $out = '<h3> Registration über Facebook hat geklappt !!! <br>';
            $out .= 'Dann noch viel Spaß auf unserer Seite ... <br> ';
            $out .= 'Über den Button gelangst du zu deiner Startseite: ';
            $out .= '<div class="eingeloggt"> 
                <form action="profile.php" method="post">
                    <input type="submit" value="Ab Gehts! "> <br> 
                </form>
            </div>';

            // Automatische Weiterleitung - derzeit aktiviert 
             header("Location:./profile");

        }

   echo $out;

require './includes/_bottom.php';
?>