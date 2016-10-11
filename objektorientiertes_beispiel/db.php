<?php
class DB extends mysqli
{
	private $connection;
	/*
	private $var = 1;
    private static $staticVar = 2;
    $this->var;
    self::$staticVar;
	*/

	function real_escape_string($str, $char = '\\')
	{
		$escaped = ereg_replace('[%_]', $char . '\0', $str);
		return addcslashes($escaped, '%_');
	}

	public function connect($host, $database, $username, $password)
	{
		$this->connection = new mysqli($host, $username, $password, $database);
		if(mysqli_connect_errno())
			throw new Exception(__METHOD__ . '->' . mysqli_connect_error());
	}
	public function close()
	{
		if(!$this->connection)
			return false;
		$this->connection->close();
	}
	
	public function query($sql, $return = 'affected', $result_mode = MYSQLI_USE_RESULT)
	{
		if(!$this->connection)
			throw new Exception(__METHOD__ . '->Keine Datenbankverbindung!');
		
		$data = array();
		if($result = $this->connection->query($sql, $result_mode)) 
		{
			if($return == 'affected') $data = $this->connection->affected_rows; 
			else if($return == 'num') $data = $result->num_rows;
			else if($return == 'id') $data = $this->connection->insert_id;
			else if($return == 'assoc') 
			{
				while($row = $result->fetch_assoc())
					$data[] = $row;
			}
			else if($return == 'numeric')
			{
				while($row = $result->fetch_assoc())
					$row = $result->fetch_array(MYSQLI_NUM);
			}
			else if($return == 'fields')
			{
				while($row = $result->fetch_fields())
					$data[] = $row;
			}
			if(is_object($result))
				$result->close();
		}
		else
		{
			throw new Exception(__METHOD__ . '->' . $this->connection->error . '->' . $sql);
		}
		return $data;
	}
	
	public function startTransaction($isolation_level = "SERIALIZABLE")
	{
		if(!$this->connection)
			return false;
		$isolation_level = strtoupper($isolation_level);
		$done = $this->query("SET TRANSACTION ISOLATION LEVEL {" . $isolation_level . "};", "bool");
		$done = ($done && $this->query("SET AUTOCOMMIT=0;", "bool"));
		return ($done && $this->query("START TRANSACTION;", "bool"));
	}
	public function commit()
	{
		if(!$this->connection)
			return false;
		if(!$this->query("COMMIT;", "bool"))
			return false;
		$this->query("SET AUTOCOMMIT=1;", "bool");
		return true;
	}
	public function rollback()
	{
		if(!$this->connection)
			return false;
		if(!$this->query("ROLLBACK;", "bool"))
			return false;
		$this->query("SET AUTOCOMMIT=1;");
		return true;
	} 
}	
?>