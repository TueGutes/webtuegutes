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
	function sendEmail($subject, $message)
	{
		if($this->loggedIn())
			sendEmail($this->getEmail(), $subject, $message);
	}
	
	function redirect($to)
	{
		header('Location: ' . $to);
	}
}	
?>