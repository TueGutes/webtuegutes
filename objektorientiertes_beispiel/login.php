<?php
	header('Content-Type: text/html; charset=UTF-8');
	
	if($_SERVER['REQUEST_METHOD'] === 'POST') // post only kein get
	{	
		session_start();

		$method = $_POST['method'];
		
		if($method == 'logout') // login.php könnte auch das ausloggen übernehmen 
		{
			session_start();
			session_destroy();
			exit;
		}
		
		
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		$continueLogin = false; // nur wenn der login erfolgreich war werden die session vars gesetzt
		
		//====Database====
		include('./db.php');
		$db = new DB();
		try 
		{
			$db->connect('localhost', 'DB_NAME', 'DB_USERNAME', 'DB_PASSWORD'); // diese infos später in eine config.php auslagern, die dann alle wichtigen defines enthält
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		};

		$db->startTransaction();
		try
		{
			$stuffToSelect = "id, username, firstname, lastname, male, password, email, regDateTime";
			$dataset = $db->query("SELECT " . $stuffToSelect . " FROM user WHERE username = '" . $username . "'", 'assoc');
			
			if($dataset[0]['id'] == '' || $dataset[0]['id'] == NULL) // username wurde nicht gefunden... vielleicht wurde die Email angegeben
				$dataset = $db->query("SELECT " . $stuffToSelect . " FROM user WHERE email = '" . $username . "'", 'assoc');
			
			if($dataset[0]['password'] == md5($dataset[0]['regDateTime'] . $password)) // ist der gespeicherte password hash == md5(seed . eingegebenes_passwort)
			{
				$id = $dataset[0]['id'];
				$db->query("UPDATE user SET lastLogin = '" . date('Y-m-d H:i:s') . "' WHERE id = '" . $id . "'"); // lastLogin wird erneuert (daher muss dies auch eine Transaktion sein)
				
				$username = $dataset[0]['username']; // da man nicht weiß, ob in $username der username oder die Email stand
				$firstname = $dataset[0]['firstname'];
				$lastname = $dataset[0]['lastname'];
				$male = $dataset[0]['male'];
				$email = $dataset[0]['email'];
				$continueLogin = true;
				// man kann auch hier die session vars setzen aber vielleicht wird ja hier noch erweitert
			}
			else $error = 'Der Benutzer existiert nicht oder das Passwort ist falsch!';
			
			$db->commit();
		}
		catch(Exception $e)
		{
			$db->rollback();
			echo $e->getMessage();
		};
		$db->close();
		//====/Database====
		
	
		if($continueLogin)
		{
			$_SESSION['logged'] = true;
			$_SESSION['id'] = $id;
			$_SESSION['username'] = $username;
			$_SESSION['firstname'] = $firstname;
			$_SESSION['lastname'] = $lastname;
			$_SESSION['male'] = $male == 1 ? true : false;
			$_SESSION['email'] = $email;
		}
		else
		{
			// $error ausgeben
		}
	}
?>