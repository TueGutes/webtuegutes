<?php
/*
*@author Henrik Huckauf
*/

class User
{
	function __construct()
	{
		//$_SESSION['username'] = '';
		//$_SESSION['email'] = '';
	}
	function login($id, $username, $email)
	{
		$_SESSION['id'] = $id;
		$_SESSION['username'] = $username;
		$_SESSION['email'] = $email;
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