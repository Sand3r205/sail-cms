<?php
	/**
	 * Sail-CMS - easy to use, single-page CMS system. Runs on PHP 7.4 and SQLite.
	 * 
	 * @author Sander Feenstra
	 * @copyright Sander Feenstra 2022
	 * @version 0.x
	 * 
	 * Sail-CMS requires the following PHP extensions:
	 *   - sqlite3
	 * 
	 * Sail-CMS requires the following write permissions
	 *   - cms/data/
	 * 	 - cms/password/
	 */
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Sail CMS</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<script type="text/javascript" src="static/js/login.js"></script>
	</head>
	<body>
		<?php
			$logoutDiv = '<div id="logout">Uitloggen</div>';
			$loginDiv = '<div id="login">Inloggen</div>';

			if (isset($_COOKIE['token'])) {
				include __DIR__ . '/cms/includes/UserDatabase.class.php';

				$userDB = new UserDatabase();
				$result = $userDB->authorise($_COOKIE['token']);
				$userDB->close();

				if ($result) {
					echo $logoutDiv;
				} else {
					echo $loginDiv;
				}
			} else {
				echo $loginDiv;
			}
		?>
	</body>
</html>