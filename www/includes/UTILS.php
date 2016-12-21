<?php
/**
 * Sammlung von Hilfsklassen und Hilffunktionen
 *
 * Bietet eine Sammlung von Hilfsklassen und Hilfsfunktionen, um Redundanz zu vermeiden
 *
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 */

/*
* DateHandler
*@author Henrik Huckauf
*/
class DateHandler
{
	/*private $year = 0;
	private $month = 0;
	private $day = 0;
	private $hours = 0;
	private $minutes = 0;
	private $seconds = 0;*/
	private $date;
	/*
	private $var = 1;
    private static $staticVar = 2;
    $this->var;
    self::$staticVar;
	*/

	function __construct()
	{
		$this->date = DateTime::createFromFormat('Y-m-d H:i:s', '1970-01-01 00:00:00');
	}
	public static function isValid($date, $format = 'Y-m-d H:i:s')
	{
		$d = DateTime::createFromFormat($format, $date);
		return $d !== false && !array_sum($d->getLastErrors()) /*verhindert überlauf wie 32.1. -> 1.2.*/;
		/*list($y, $m, $d) = array_pad(explode('-', $date, 3), 3, 0);
		return ctype_digit($y . $m . $d) && checkdate($m, $d, $y); //ctype_digit -> nur Ziffern?*/
	}
	public function set($date) // YYYY-MM-DD hh:mm:ss
	{
		if(self::isValid($date))
		{
			//if(strpos($date, " ") !== false) // !== false ist nötig, da 0 ein gültiger index von strpos sein kann aber 0 in php auch als false behandelt wird daher funktioniert kein !strpos...
			$this->date = DateTime::createFromFormat('Y-m-d H:i:s', $date);
			return $this;
		}
		else if(self::isValid($date, 'Y-m-d'))
		{
			$this->date = DateTime::createFromFormat('Y-m-d', $date);
			$this->date->setTime(0, 0, 0);
			return $this;
		}
		else if(self::isValid($date, 'd.m.Y H:i:s'))
		{
			$this->date = DateTime::createFromFormat('d.m.Y H:i:s', $date);
			return $this;
		}
		else if(self::isValid($date, 'd.m.Y'))
		{
			$this->date = DateTime::createFromFormat('d.m.Y', $date);
			$this->date->setTime(0, 0, 0);
			return $this;
		}
		else if(self::isValid($date, 'd.m.Y H:i'))
		{
			$this->date = DateTime::createFromFormat('d.m.Y H:i', $date);
			$this->setSeconds(0);
			return $this;
		}
		return false;
	}
	public function get($format = 'Y-m-d H:i:s')
	{
		return $this->date->format($format);
	}
	public function getYear()
	{
		return $this->date->format('Y');
	}
	public function getMonth()
	{
		return $this->date->format('m');
	}
	public function getDay()
	{
		return $this->date->format('d');
	}
	public function getHours()
	{
		return $this->date->format('H');
	}
	public function getMinutes()
	{
		return $this->date->format('i');
	}
	public function getSeconds()
	{
		return $this->date->format('s');
	}
	
	public function setYear($w)
	{
		$this->date->setDate($w, $this->getMonth(), $this->getDay());
		return $this;
	}
	public function setMonth($w)
	{
		$this->date->setDate($this->getYear(), $w, $this->getDay());
		return $this;
	}
	public function setDay($w)
	{
		$this->date->setDate($this->getYear(), $this->getMonth(), $w);
		return $this;
	}
	public function setHours($w)
	{
		$this->date->setTime($w, $this->getMinutes(), $this->getSeconds());
		return $this;
	}
	public function setMinutes($w)
	{
		$this->date->setTime($this->getHours(), $w, $this->getSeconds());
		return $this;
	}
	public function setSeconds($w)
	{
		$this->date->setTime($this->getHours(), $this->getMinutes(), $w);
		return $this;
	}
	
	public function diff($date)
	{
		return strtotime($this->date->get()) - strtotime($date);
	}
	public function equals($date)
	{
		return $this->diff($date) == 0;
	}
	public function before($date)
	{
		return $this->diff($date) < 0;
	}
	public function after($date)
	{
		return $this->diff($date) > 0;
	}
}

/*
* PrivacyHandler
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