<?php

class db extends mysqli
{
    private $connection;
    public function connect()
    {
        $this->connection = new mysqli('localhost', 'root', '', '');
		if(mysqli_connect_errno())
			throw new Exception(__METHOD__ . '->' . mysqli_connect_error());
    }
    public function db_close(mysqli $db)
    {
        if(!$this->connection)
			return false;
		$this->connection->close();
    }
    // function query needs modify
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
}
