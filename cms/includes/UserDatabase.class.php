<?php
	/**
	 * User Database instance for storing user data. Initialises with an admin account when database does not exist.
	 */

	class UserDatabase extends SQLite3
	{
		function __construct()
		{
			parent::__construct('cms/data/user.db');
			
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
				$insertStatement = $this->prepare("INSERT INTO users (UserName, PasswordHash) VALUES ('admin', 'c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec')");
				$insertStatement->execute();
			}
		}
	}

	$userDB = new UserDatabase();

	$userDB->close();
?>