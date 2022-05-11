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

	// TODO: Should only be called when the users wants to log in.
	include('cms/includes/UserDatabase.class.php');
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Sail CMS</title>
	</head>
	<body>
		<p>Sail CMS</p>
	</body>
</html>