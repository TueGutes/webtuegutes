<?php
/**
 * Testklasse für Klasse "PrivacyHandler".
 *
 * @author Alexander Gauggel <alexander.gauggel@stud.hs-hannover.de>
 */
require_once('../../www/includes/UTILS.php');

class TestPrivacyHandler extends PHPUnit_Framework_TestCase
{	
	public function setUp() { }
	
	public function tearDown() { }
	
	public function testGetPrivacyString()
	{	 
		$lPrivacyString = "01000";
		
		$lPrivacyHandler = new PrivacyHandler($lPrivacyString);
		
		$lExpected = 1;
		$lIndex = 1;
		
		return $this->assertEquals($lExpected, $lPrivacyHandler->get($lIndex));
	}
		
	public function testGetSetPrivacyString()
	{	 
		$lPrivacyString = "01000";
		
		$lPrivacyHandler = new PrivacyHandler($lPrivacyString);
		
		$lExpected = 0;
		$lIndex = 2;
		
		return $this->assertEquals($lExpected, $lPrivacyHandler->get($lIndex));
	}
}
		
?>