<?php
date_default_timezone_set('Europe/Amsterdam');

// Connect to mysql on the localhost:3307, use the database "bibliotheek".
$databaseConnectionString = "mysql:host=192.168.1.1;port=3306;dbname=projecten;charset=utf8";
// Use this username.
$username = "petapp";
// And this password.
$password = "Geheim1234!";

// try to connect to the database.
try {
	// Create a PDO object using the connection string with the correct username and password.
	$database = new PDO($databaseConnectionString, $username, $password);
	// Throw exceptions with error info when stuff fails.
	$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
//	print "Connection to the database succeeded!";
} catch (Exception $ex) 

{
	// Let the user know what we were doing and also provide the actual exception message.
	echo "Failed to connect to the database : ".$ex->getMessage();
}
?>