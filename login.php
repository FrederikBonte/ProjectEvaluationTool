<?php
include "templates/header_login.txt";
require_once "common/config.inc.php";
require_once "common/debug.php";

session_start();

if (array_key_exists("docent", $_SESSION))
{
	// Already logged in...
	debug_log("Already logged in.");
	header("Location: index.php");
	exit();
}

if (array_key_exists("login", $_REQUEST)) 
{
	// Attempt to login
	debug_log("Attempt to login...");
	debug_dump($_REQUEST);
	checkLogin($_REQUEST["username"], $_REQUEST["password"]);
	if (array_key_exists("user_id", $_SESSION))
	{
		header("Location: index.php");
		exit();
	}
	else
	{
		debug_warning("Login failed.");
	}
}

?>
<form action="login.php">
	Username: <input type="text" name="username" /><br />
	Password: <input type="password" name="password" />
	<input type="submit" name="login" value="Inloggen" />
</form>
</body>
</html>
<?php
function checkLogin($username, $password)
{
	global $database;
	
	$sql = 
	"SELECT * ".
	"FROM docent ".
	"WHERE username=:field1 AND password = MD5(concat(:field2, salt))";
	
	echo "<!-- $sql -->\n\r";
	
	// Prepare a query...
	$stmt = $database->prepare($sql);
	// Additional database
	$data = [
		"field1" => $username,
		"field2" => $password
	];
	
	// Activate the query...
	$stmt->execute($data);
	// Retrieve one record.
	$row=$stmt->fetch(PDO::FETCH_ASSOC);
	// Return the found users id.
	if ($row) 
	{
		$_SESSION["docent"] = $row["code"];
		$_SESSION["name"] = $row["aanspreekvorm"];
		
	}
	else
	{
		debug_warning("Onbekende login gegevens");
		return null;
	}
}
?>