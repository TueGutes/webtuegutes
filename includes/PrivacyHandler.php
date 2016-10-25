<?php
/*
*@author Henrik Huckauf
*/

class PrivacyHandler
{
	private $privacyFlags;
	/*
	private $var = 1;
    private static $staticVar = 2;
    $this->var;
    self::$staticVar;
	*/
	public static $NAME = 0;
	public static $EMAIL = 1;
	public static $BIRTHDAY = 2;
	public static $BIRTHDAY_YEAR = 3;
	public static $DESCRIPTION = 4;

	function __construct($privacyString)
	{
		$this->setPrivacyString($privacyString);
	}
	public function setPrivacyString($privacyString)
	{
		$this->$privacyFlags = str_split($privacyString); // explode benötigt zwingend delimiter (!= '')
	} 
	// $ph->get($ph::EMAIL);
	public function get($key)
	{
		return $privacyFlags[$key] === "1" ? true : false;
	}
}	
?>