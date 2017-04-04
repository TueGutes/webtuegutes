<?php
/**
 * Klasse für $_USER Objekt
 *
 * Bietet Funktionen, um die SESSION des Users zu handlen
 *
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 */

class User
{
	function __construct()
	{
		//$_SESSION['username'] = '';
		//$_SESSION['email'] = '';
	}
	function login($id, $username, $email, $firstname, $lastname)
	{
		$_SESSION['id'] = $id;
		$_SESSION['username'] = $username;
		$_SESSION['email'] = $email;
		$_SESSION['firstname'] = $firstname;
		$_SESSION['lastname'] = $lastname;
		$_SESSION['loggedIn'] = true;
	}
	function logout()
	{
		//if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'])
			session_destroy();
		header('Location: ./');
	}
	
	function loggedIn()
	{
		return isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'];
	}
	
	function getID()
	{
		return $this->loggedIn() ? $_SESSION['id'] : false;
	}
	function getUsername()
	{
		return $this->loggedIn() ? $_SESSION['username'] : false;
	}
	function getEmail()
	{
		return $this->loggedIn() ? $_SESSION['email'] : false;
	}
	function getFirstname()
	{
		return $this->loggedIn() ? $_SESSION['firstname'] : false;
	}
	function getLastname()
	{
		return $this->loggedIn() ? $_SESSION['lastname'] : false;
	}
	function hasGroup($groupID)
	{
		return $this->loggedIn() ? $this->get('group') >= $groupID : false;
	}
	function get($key)
	{
		return $this->loggedIn() ? $_SESSION[$key] : false;
	}
	function set($key, $value)
	{
		if($this->loggedIn())
			$_SESSION[$key] = $value;
	}
	function sendEmail($subject, $message)
	{
		if($this->loggedIn())
			sendEmail($this->getEmail(), $subject, $message);
	}
	
	function redirect($to)
	{
		header('Location: ' . $to);
	}
	
	function getProfileImagePathOf($id, $size = 512)
	{
		$path = "img/profiles/" . $id . "/" . $size . "x" . $size . ".png";
		if($this->loggedIn() && file_exists($path))
			return $path;
		
		$addition = 'other';
		$gender = $this->get('gender');
		if($gender !== false && substr($this->get('privacykey'), 7, 1) === "1")
			switch($gender)
			{
				case 'm':
					$addition = 'male';
					break;
				case 'w':
					$addition = 'female';
					break;
			}
		return "img/profiles/standard_" . $addition . ".png";
	}
	function getProfileImagePath($size = 512)
	{
		return $this->getProfileImagePathOf($this->getID(), $size);
	}
}	
?>



