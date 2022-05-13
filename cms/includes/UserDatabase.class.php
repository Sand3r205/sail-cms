<?php
	/**
	 * User Database instance for storing user data. Initialises with an admin account when database does not exist.
	 */

	class UserDatabase extends SQLite3
	{
		const TOKEN_DURATION = 3600;

		public function __construct()
		{
			parent::__construct(__DIR__ . "/../data/user.db");
			
			self::initialise();
		}

		private function initialise()
		{
			$createUsersStatement = self::prepare("CREATE TABLE IF NOT EXISTS users (Id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT, UserName varchar(255) NOT NULL UNIQUE, PasswordHash char(128))");
			$createUsersStatement->execute();
			$createUsersStatement->close();

			$selectStatement = self::prepare("SELECT UserName FROM users");
			$selectResult = $selectStatement->execute();

			if (!$username = $selectResult->fetchArray()) {
				$rand = rand();
				$password = hash('sha512', $rand);
				$hash = hash('sha512', $password);

				$file = fopen("cms/password/newpass.txt", "w");
				fwrite($file, $password);
				fclose($file);

				$insertStatement = self::prepare("INSERT INTO users (UserName, PasswordHash) VALUES ('admin', :hash)");
				$insertStatement->bindValue(':hash', $hash, SQLITE3_TEXT);
				$insertStatement->execute();
				$insertStatement->close();
			} else if (file_exists(__DIR__ . "/../password/pass.txt")) {
				$file = fopen(__DIR__ . "/../password/pass.txt", "r");
				$password = fgets($file);

				$hash = hash('sha512', $password);
				
				$updateStatement = self::prepare("UPDATE users SET PasswordHash = :hash WHERE UserName = 'admin';");
				$updateStatement->bindValue(":hash", $hash, SQLITE3_TEXT);
				$updateStatement->execute();
				$updateStatement->close();

				fclose($file);
				unlink(__DIR__ . "/../password/pass.txt");
			}

			$selectStatement->close();

			$createSessionStatement = self::prepare("CREATE TABLE IF NOT EXISTS sessions (UserId INTEGER, Token char(128), Expires INTEGER)");
			$createSessionStatement->execute();
			$createSessionStatement->close();
		}

		public function authenticate(string $username, string $password)
		{
			$selectStatement = self::prepare("SELECT Id, PasswordHash FROM users WHERE UserName=:username LIMIT 1");
			$selectStatement->bindValue(":username", $username);
			$selectResult = $selectStatement->execute();

			if (!$res = $selectResult->fetchArray()) {
				return false;
			} else {
				$inputHash = hash('sha512', $password);

				if ($inputHash == $res[1]) {
					$rand = rand();
					$token = hash('sha512', $rand);

					self::logoutByUserId($res[0]);

					$insertStatement = self::prepare("INSERT INTO sessions (UserId, Token, Expires) VALUES (:userId, :token, :expires)");
					$insertStatement->bindValue(":userId", $res[0], SQLITE3_INTEGER);
					$insertStatement->bindValue(":token", $token, SQLITE3_TEXT);
					echo $TOKENDURATION;
					$insertStatement->bindValue(":expires", time() + self::TOKEN_DURATION, SQLITE3_INTEGER);
					$insertStatement->execute();
					$insertStatement->close();

					// User has logged in, hence we can delete this file.
					if (file_exists(__DIR__ . "/../password/newpass.txt")) {
						unlink(__DIR__ . "/../password/newpass.txt");
					}

					return $token;
				} else {
					return false;
				}
			}
		}

		public function authorise(string $token)
		{
			$selectStatement = self::prepare("SELECT UserId, Expires FROM sessions WHERE Token=:token LIMIT 1");
			$selectStatement->bindValue(":token", $token, SQLITE3_TEXT);
			$selectResult = $selectStatement->execute();

			if (!$res = $selectResult->fetchArray()) {
				return false;
			} else {
				if ($res[1] < time()) {
					return false;
				}

				return $res[0];
			}
		}

		public function logoutByUserId(int $userId)
		{
			$deleteStatement = self::prepare("DELETE FROM sessions WHERE UserId=:userId");
			$deleteStatement->bindValue(":userId", $userId, SQLITE3_INTEGER);
			$deleteStatement->execute();
			$deleteStatement->close();
		}

		public function logoutByToken(string $token)
		{
			$deleteStatement = self::prepare("DELETE FROM sessions WHERE Token=:token");
			$deleteStatement->bindValue(":token", $token, SQLITE3_TEXT);
			$deleteStatement->execute();
			$deleteStatement->close();
		}

		public function getUserIdFromToken(string $token)
		{
			$selectStatement = self::prepare("SELECT UserId FROM sessions WHERE Token=:token");
			$selectStatement->bindValue(":token", $token, SQLITE3_TEXT);
			$selectResult = $selectStatement->execute();

			if (!$res = $selectResult->fetchArray()) {
				return false;
			} else {
				return $res;
			}
		}
	}
?>