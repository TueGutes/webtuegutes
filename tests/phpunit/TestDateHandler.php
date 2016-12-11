<?php
/**
 * Testklasse für Klasse "DateHandler".
 *
 * @author Alexander Gauggel <alexander.gauggel@stud.hs-hannover.de>
 */
require_once('../../www/includes/UTILS.php');

class TestDateHandler extends PHPUnit_Framework_TestCase
{	
	public function setUp() { }
	
	public function tearDown() { }
	
	public function testIsValidDefaultTrue()
	{	 
		$lExpected = true;
		$lDate = "2016-02-29 00:00:00";
		
		return $this->assertEquals($lExpected, DateHandler::isValid($lDate));
	}
	
	public function testIsValidDefaultFalse()
	{	 
		$lExpected = false;
		$lDate = "2015-02-29 00:00:00";
		
		return $this->assertEquals($lExpected, DateHandler::isValid($lDate));
	}
	
	public function testIsValidCustomTrue()
	{	 
		$lExpected = true;
		$lDate = "2016-02-29";
		$lFormat = "Y-m-d";
		
		return $this->assertEquals($lExpected, DateHandler::isValid($lDate, $lFormat));
	}
	
	public function testIsValidCustomFalse()
	{	 
		$lExpected = false;
		$lDate = "2015-02-29";
		$lFormat = "Y-m-d";
		
		return $this->assertEquals($lExpected, DateHandler::isValid($lDate, $lFormat));
	}
	
	// TODO: Improve.
	public function testSetGetDefaultTrue()
	{
		$lDateHandler = new DateHandler();
		
		$lExpected = "2016-02-29 00:00:00";
		$lDate = $lExpected;
		
		$lDateHandler->set($lDate);
		
		return $this->assertEquals($lExpected, $lDateHandler->get());
	}
	
	public function testGetYear()
	{
		$lDateHandler = new DateHandler();
		
		$lExpected = 2016;
		$lDate = "2016-02-29 00:00:00";
		
		$lDateHandler->set($lDate);
		
		return $this->assertEquals($lExpected, $lDateHandler->getYear());
	}
	
	public function testGetMonth()
	{
		$lDateHandler = new DateHandler();
		
		$lExpected = 2;
		$lDate = "2016-02-29 00:00:00";
		
		$lDateHandler->set($lDate);
		
		return $this->assertEquals($lExpected, $lDateHandler->getMonth());
	}
	
	public function testGetDay()
	{
		$lDateHandler = new DateHandler();
		
		$lExpected = 29;
		$lDate = "2016-02-29 00:00:00";
		
		$lDateHandler->set($lDate);
		
		return $this->assertEquals($lExpected, $lDateHandler->getDay());
	}
	
	public function testGetHours()
	{
		$lDateHandler = new DateHandler();
		
		$lExpected = 23;
		$lDate = "2016-02-29 23:59:59";
		
		$lDateHandler->set($lDate);
		
		return $this->assertEquals($lExpected, $lDateHandler->getHours());
	}
	
	public function testGetMinutes()
	{
		$lDateHandler = new DateHandler();
		
		$lExpected = 59;
		$lDate = "2016-02-29 23:59:59";
		
		$lDateHandler->set($lDate);
		
		return $this->assertEquals($lExpected, $lDateHandler->getMinutes());
	}
	
	public function testGetSeconds()
	{
		$lDateHandler = new DateHandler();
		
		$lExpected = 59;
		$lDate = "2016-02-29 23:59:59";
		
		$lDateHandler->set($lDate);
		
		return $this->assertEquals($lExpected, $lDateHandler->getSeconds());
	}
	
	public function testSetGetYear()
	{
		$lDateHandler = new DateHandler();
		
		$lExpected = 2012;
		$lYear = $lExpected;
		$lDate = "2016-02-29 23:59:59";
		
		$lDateHandler->set($lDate);
		
		return $this->assertEquals($lExpected,$lDateHandler->getYear($lDateHandler->setYear($lYear)));
	}
	
	public function testSetGetMonth()
	{
		$lDateHandler = new DateHandler();
		
		$lExpected = 01;
		$lMonth = $lExpected;
		$lDate = "2016-02-29 23:59:59";
		
		$lDateHandler->set($lDate);
		
		return $this->assertEquals($lExpected,$lDateHandler->getMonth($lDateHandler->setMonth($lMonth)));
	}
	
	public function testSetGetDay()
	{
		$lDateHandler = new DateHandler();
		
		$lExpected = 31;
		$lDay = $lExpected;
		$lDate = "2016-01-29 23:59:59";
		
		$lDateHandler->set($lDate);
		
		return $this->assertEquals($lExpected,$lDateHandler->getDay($lDateHandler->setDay($lDay)));
	}
	
	public function testSetGetHours()
	{
		$lDateHandler = new DateHandler();
		
		$lExpected = 0;
		$lHours = $lExpected;
		$lDate = "2016-01-29 23:59:59";
		
		$lDateHandler->set($lDate);
		
		return $this->assertEquals($lExpected,$lDateHandler->getHours($lDateHandler->setHours($lHours)));
	}
	
	public function testSetGetMinutes()
	{
		$lDateHandler = new DateHandler();
		
		$lExpected = 0;
		$lMinutes = $lExpected;
		$lDate = "2016-01-29 23:59:59";
		
		$lDateHandler->set($lDate);
		
		return $this->assertEquals($lExpected,$lDateHandler->getMinutes($lDateHandler->setMinutes($lMinutes)));
	}
	
	public function testSetGetSeconds()
	{
		$lDateHandler = new DateHandler();
		
		$lExpected = 0;
		$lSeconds = $lExpected;
		$lDate = "2016-01-29 23:59:59";
		
		$lDateHandler->set($lDate);
		
		return $this->assertEquals($lExpected,$lDateHandler->getSeconds($lDateHandler->setSeconds($lSeconds)));
	}
}
		
?>