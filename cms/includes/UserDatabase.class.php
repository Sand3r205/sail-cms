<?php
	/**
	 * User Database instance for storing user data. Initialises with an admin account when database does not exist.
	 */

	class UserDatabase extends SQLite3
	{
		function __construct()
		{
			parent::__construct("cms/data/user.db");
			
			$this->initialise();
		}

		function initialise()
		{
			$createStatement = $this->prepare("CREATE TABLE IF NOT EXISTS users (ID INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, UserName varchar(255) NOT NULL UNIQUE, PasswordHash char(128))");
			$createStatement->execute();
			$createStatement->close();

			$selectStatement = $this->prepare("SELECT UserName FROM users");
			$selectResult = $selectStatement->execute();

			if (!$username = $selectResult->fetchArray()) {
				$rand = rand();
				$password = hash('sha512', $rand);
				$hash = hash('sha512', $password);

				$file = fopen("cms/password/newpass.txt", "w");
				fwrite($file, $password);
				fclose($file);

				$insertStatement = $this->prepare("INSERT INTO users (UserName, PasswordHash) VALUES ('admin', '" . $hash . "')");
				$insertStatement->execute();
			} else if (file_exists("cms/password/pass.txt") && !file_exists("cms/password/newpass.txt")) {
				$file = fopen("cms/password/pass.txt", "r");
				$password = fgets($file);

				$hash = hash('sha512', $password);
				
				$updateStatement = $this->prepare("UPDATE users SET PasswordHash = '" . $hash . "' WHERE UserName = 'admin';");
				$updateStatement->execute();

				fclose($file);
				unlink("cms/password/pass.txt");
			}
		}
	}

	$userDB = new UserDatabase();

	$userDB->close();
?>