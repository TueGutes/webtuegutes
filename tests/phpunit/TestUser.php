<?php
/**
 * Testklasse für Klasse "User".
 *
 * @author Alexander Gauggel <alexander.gauggel@stud.hs-hannover.de>
 */
require_once('../../www/includes/user.php');

class TestUser extends PHPUnit_Framework_TestCase
{	
	public function setUp() { }
	
	public function tearDown() { }
	
	public function testLoggedInTrue()
	{	
		$lExpected = true;
		
		$lUser = new User();		
		$lUser->login(1, "testuser", "testEMail", "testFirstName", "testLastName");
		
		return $this->assertEquals($lExpected, $lUser->loggedIn());
	}
	
	public function testLoggedInFalse()
	{	
		$lExpected = false;
		
		$lUser = new User();		
		
		return $this->assertEquals($lExpected, $lUser->loggedIn());
	}
	
	function testGetID()
	{		
		$lExpected = 1;
		
		$lUser = new User();		
		$lUser->login(1, "testuser", "testEMail", "testFirstName", "testLastName");
		
		return $this->assertEquals($lExpected, $lUser->getID());
	}
	
	function testGetIDFalse()
	{		
		$lExpected = false;
		
		$lUser = new User();		
		
		return $this->assertEquals($lExpected, $lUser->getID());
	}
	
	function testGetUsername()
	{
		$lExpected = "testuser";
		
		$lUser = new User();		
		$lUser->login(1, "testuser", "testEMail", "testFirstName", "testLastName");
		
		return $this->assertEquals($lExpected, $lUser->getUsername());
	}
	
	function testGetUsernameFalse()
	{
		$lExpected = false;
		
		$lUser = new User();		
		
		return $this->assertEquals($lExpected, $lUser->getUsername());
	}
	
	function testGetEmail()
	{
		$lExpected = "testEMail";
		
		$lUser = new User();		
		$lUser->login(1, "testuser", "testEMail", "testFirstName", "testLastName");
		
		return $this->assertEquals($lExpected, $lUser->getEmail());
	}
	
	function testGetEmailFalse()
	{
		$lExpected = false;
		
		$lUser = new User();		
		
		return $this->assertEquals($lExpected, $lUser->getEmail());
	}
	
	function testGetFirstname()
	{	
		$lExpected = "testFirstName";
		
		$lUser = new User();		
		$lUser->login(1, "testuser", "testEMail", "testFirstName", "testLastName");
		
		return $this->assertEquals($lExpected, $lUser->getFirstname());
	}
	
	function testGetFirstnameFalse()
	{	
		$lExpected = false;
		
		$lUser = new User();		
		
		return $this->assertEquals($lExpected, $lUser->getFirstname());
	}
	
	function testGetLastname()
	{
		$lExpected = "testLastName";
		
		$lUser = new User();		
		$lUser->login(1, "testuser", "testEMail", "testFirstName", "testLastName");
		
		return $this->assertEquals($lExpected, $lUser->getLastname());
	}
	
	function testGetLastnameFalse()
	{
		$lExpected = false;
		
		$lUser = new User();		
		
		return $this->assertEquals($lExpected, $lUser->getLastname());
	}
	
	function testSetGet()
	{
		$lExpected = "testValue";
		
		$lIndex = 2;
		$lUser = new User();		
		$lUser->login(1, "testuser", "testEMail", "testFirstName", "testLastName");
		$lUser->set($lIndex, "testValue");
		
		return $this->assertEquals($lExpected, $lUser->get($lIndex));
	}
	
	function testSetGetFalse()
	{
		$lExpected = false;
		
		$lIndex = 2;
		$lUser = new User();		
		$lUser->set($lIndex, "testValue");
		
		return $this->assertEquals($lExpected, $lUser->get($lIndex));
	}
	
	function testGetFalse()
	{
		$lExpected = false;
		
		$lIndex = 2;
		$lUser = new User();		
		
		return $this->assertEquals($lExpected, $lUser->get($lIndex));
	}
	
	function testGetProfileImagePathDefaultOther()
	{
		$lExpected = "img/profiles/standard_other.png";
		
		$lUser = new User();		
		$lUser->login(1, "testuser", "testEMail", "testFirstName", "testLastName");
		$lUser->set("gender", "");
		$lUser->set("privacykey", 11111111);
		
		return $this->assertEquals($lExpected, $lUser->getProfileImagePath());
	}
	
	function testGetProfileImagePathDefaultFemale()
	{
		$lExpected = "img/profiles/standard_female.png";
	
		$lUser = new User();		
		$lUser->login(1, "testuser", "testEMail", "testFirstName", "testLastName");
		$lUser->set("gender", "w");
		$lUser->set("privacykey", 11111111);
		
		return $this->assertEquals($lExpected, $lUser->getProfileImagePath());
	}
	
	function testGetProfileImagePathDefaultMale()
	{
		$lExpected = "img/profiles/standard_male.png";
		
		$lUser = new User();		
		$lUser->login(1, "testuser", "testEMail", "testFirstName", "testLastName");
		$lUser->set("gender", "m");
		$lUser->set("privacykey", 11111111);
		
		return $this->assertEquals($lExpected, $lUser->getProfileImagePath());
	}
}
		
?>