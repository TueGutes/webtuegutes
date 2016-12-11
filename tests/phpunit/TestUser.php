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
	
	public function testLoggedIn()
	{	
		$lUser = new User();		
		$lUser->login(1, "testuser", "testEMail", "testFirstName", "testLastName");
		return $this->assertEquals(true, $lUser->loggedIn());
	}
	
	function testGetID()
	{		
		$lUser = new User();		
		$lUser->login(1, "testuser", "testEMail", "testFirstName", "testLastName");
		return $this->assertEquals(1, $lUser->getID());
	}
	
	function testGetUsername()
	{
		$lUser = new User();		
		$lUser->login(1, "testuser", "testEMail", "testFirstName", "testLastName");
		return $this->assertEquals("testuser", $lUser->getUsername());
	}
	
	function testGetEmail()
	{
		$lUser = new User();		
		$lUser->login(1, "testuser", "testEMail", "testFirstName", "testLastName");
		return $this->assertEquals("testEMail", $lUser->getEmail());
	}
	
	function testGetFirstname()
	{	
		$lUser = new User();		
		$lUser->login(1, "testuser", "testEMail", "testFirstName", "testLastName");
		return $this->assertEquals("testFirstName", $lUser->getFirstname());
	}
	
	function testGetLastname()
	{
		$lUser = new User();		
		$lUser->login(1, "testuser", "testEMail", "testFirstName", "testLastName");
		return $this->assertEquals("testLastName", $lUser->getLastname());
	}
	
	function testGet()
	{
		$lUser = new User();		
		$lUser->login(1, "testuser", "testEMail", "testFirstName", "testLastName");
		$lUser->set(2, "testValue");
		return $this->assertEquals("testValue", $lUser->get(2));
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