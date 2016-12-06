<?php

//Include Facebook SDK
require_once 'inc/facebook.php';

/*
 * Configuration and setup FB API
 */
$appId = '358601767847484'; //Facebook App ID
$appSecret = 'caf53e765684b8002576fe037f4085ba'; // Facebook App Secret
$redirectURL = $HOST.'/loginFacebook.php'; // Callback URL
$fbPermissions = 'public_profile, email, user_friends';  //Required facebook permissions

//Call Facebook API
$facebook = new Facebook(array(
  'appId'  => $appId,
  'secret' => $appSecret
));
$fbUser = $facebook->getUser();
?>